<?php

namespace App\Frontend\Controller;

use App\Entity\Comment;
use App\Repository\TagRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;
    private $commentRepository;
    private $params;

    public function __construct(
    ProductRepository $productRepository, 
    CategoryRepository $categoryRepository,
    CommentRepository $commentRepository,
    ContainerBagInterface $params){
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->params = $params;
    }

    #[Route('/category/{slugCategory}', name: 'view_category')]
    public function viewAllGame($slugCategory): Response
    {
        $category = $this->categoryRepository->findOneBy(["slug" => $slugCategory]);
        $products = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],$this->params->get('limitProductCategory'));
        if ($slugCategory == 'jeux') {
            $bg = 'img/bg-game.jpg';
        }elseif ($slugCategory == 'goodies') {
            $bg = 'img/bg-goodies.jpg';
        }elseif ($slugCategory == 'poster') {
            $bg = 'img/bg-poster.jpg';
        }else{
            $bg = null;
        }
        return $this->render('frontend/pages/category.html.twig',[
            'category' => $slugCategory,
            'bg' => $bg,
            'products' => $products,
        ]);
    }

    #[Route('/next-category/{slugCategory}/{start}/{action}', name: 'view_category_next_product')]
    public function nextProduct($slugCategory,$start,$action){
        
        $category = $this->categoryRepository->findOneBy(["slug" => $slugCategory]);

        if ($action == 'next') {
            $start = $start + $this->params->get('limitProductCategory');
            $nextProduct = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],1,$start + $this->params->get('limitProductCategory'));
            if (!$nextProduct) {
                $classButtonNext = "disabled";
            }else{
                $classButtonNext = null;
            }
            $classButtonPrev = null;
        }elseif ($action == 'prev') {
            $start = $start - $this->params->get('limitProductCategory');
            if ($start == 0 || $start < 0) {
                $classButtonPrev = "disabled";
            }else{
                $classButtonPrev = null;
            }
            $classButtonNext = null;
        }

        $products = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],$this->params->get('limitProductCategory'),$start);
        
        return $this->render('frontend/_parts/nextProduct.html.twig',[
            'category' => $slugCategory,
            'products' => $products,
            'start' => $start,
            'classButtonNext' => $classButtonNext,
            'classButtonPrev' => $classButtonPrev,
        ]);
    }

    #[Route('/produit/{idProduct}', name: 'view_product')]
    public function viewProduct(Request $request, SessionInterface $session, ProductRepository $productRepository ,$idProduct): Response
    {
        $product = $this->productRepository->findOneBy(["id" => $idProduct]);
        $comments = $this->commentRepository->findBy(["product" => $product]);
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        
        $totalAmount = 0;
        $numberOfItems = 0;
        if (count($panierWithData) > 0) {
            foreach ($panierWithData as $item) {
                
                $totalItem = $item['product']->getPrice() * $item['quantity'];
                $totalAmount += $totalItem;
                $numberOfItems = $numberOfItems + $item['quantity'];
            }
        }
        $totalAmount = $totalAmount / 100;
        return $this->render('frontend/pages/product.html.twig', [
            "product" => $product,
            "comments" => $comments,
            'numberOfItems' => $numberOfItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    #[Route('/ajout-commentaire/{idProduct}', name: 'add_comment_product')]
    public function addComment(Request $request , EntityManagerInterface $em,$idProduct): Response
    {
        $note = $request->request->get('noteProduct');
        $message = $request->request->get('messageComment');
        $product = $this->productRepository->findOneBy(["id" => $idProduct]);
        $createdComment = new DateTimeImmutable();
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setText($message)->setNote($note)->setCreatedAt($createdComment)->setUser($user)->setProduct($product);
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute('view_product',['idProduct' => $product->getId()]);
    }
}
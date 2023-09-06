<?php

namespace App\Frontend\Controller;

use App\Repository\TagRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

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
    public function viewProduct(Request $request ,$idProduct): Response
    {
        $product = $this->productRepository->findOneBy(["id" => $idProduct]);
        $comments = $this->commentRepository->findBy(["product" => $product]);
        $session = $request->getSession();
        return $this->render('frontend/pages/product.html.twig', [
            "product" => $product,
            "comments" => $comments
        ]);
    }
}

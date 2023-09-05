<?php

namespace App\Frontend\Controller;

use App\Form\ContactType;
use App\Repository\TagRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $tagRepository;
    private $productRepository;
    private $categoryRepository;
    private $commentRepository;

    public function __construct(TagRepository $tagRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository, CommentRepository $commentRepository){
        $this->tagRepository = $tagRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }
    
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $tags = $this->tagRepository->findAll();
        $lastsProducts = $this->productRepository->findBy([], ["published_at" => "DESC"], 15);
        $discountsProducts = $this->productRepository->getRandomProduct(4);
        return $this->render('frontend/pages/home.html.twig', [
            'tags' => $tags,
            'lastsProducts' => $lastsProducts,
            'discountsProducts' => $discountsProducts
        ]);
    }

    #[Route('/mentions-legales', name: 'mention_legales')]
    public function mentionsLegale(): Response
    {
        return $this->render('frontend/pages/mentions_legales.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'politique_de_confidentialite')]
    public function politiqueDeConfidentialite(): Response
    {
        return $this->render('frontend/pages/politique_de_confidentialite.html.twig');
    }

    #[Route('/category/{slugCategory}', name: 'view_category')]
    public function viewAllGame($slugCategory): Response
    {
        $category = $this->categoryRepository->findOneBy(["slug" => $slugCategory]);
        $products = $category->getProducts();
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
            'products' => $products
        ]);
    }

    #[Route('/produit/{idProduct}', name: 'view_product')]
    public function viewProduct($idProduct): Response
    {
        $product = $this->productRepository->findOneBy(["id" => $idProduct]);
        $comments = $this->commentRepository->findBy(["product" => $product]);
        return $this->render('frontend/pages/product.html.twig', [
            "product" => $product,
            "comments" => $comments
        ]);
    }

    #[Route('/search-product', name: 'search_product')]
    public function searcProduct(): Response
    {
        return $this->render('frontend/pages/search.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contactPage(Request $request): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        return $this->render('frontend/pages/contact.html.twig',[
            'formContact' => $contactForm->createView(),
        ]);
    }
}

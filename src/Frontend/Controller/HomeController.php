<?php

namespace App\Frontend\Controller;

use App\Repository\TagRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $tagRepository;
    private $productRepository;

    public function __construct(TagRepository $tagRepository, ProductRepository $productRepository){
        $this->tagRepository = $tagRepository;
        $this->productRepository = $productRepository;
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

    #[Route('/search-product', name: 'search_product')]
    public function searcProduct(): Response
    {
        return $this->render('frontend/pages/search.html.twig');
    }
}

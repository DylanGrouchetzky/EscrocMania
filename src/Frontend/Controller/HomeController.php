<?php

namespace App\Frontend\Controller;

use App\Entity\ContentPage;
use App\Repository\ContentPageRepository;
use App\Repository\TagRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    private $tagRepository;
    private $productRepository;

    public function __construct(TagRepository $tagRepository, ProductRepository $productRepository){
        $this->tagRepository = $tagRepository;
        $this->productRepository = $productRepository;
    }
    
    public function getNumberArticleInBasket(SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $numberOfItems = 0;
        foreach ($panierWithData as $item) {
            $numberOfItems = $numberOfItems + $item['quantity'];
        }
        return $numberOfItems;
    }
    
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $tags = $this->tagRepository->findBy([],[],8);
        $lastsProducts = $this->productRepository->findBy([], ["published_at" => "DESC"], 15);
        $discountsProducts = $this->productRepository->getRandomProduct(4);
        return $this->render('frontend/pages/home.html.twig', [
            'tags' => $tags,
            'lastsProducts' => $lastsProducts,
            'discountsProducts' => $discountsProducts,
        ]);
    }

    #[Route('/mentions-legales', name: 'mention_legales')]
    public function mentionsLegale(): Response
    {
        return $this->render('frontend/pages/default/mentions_legales.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'politique_de_confidentialite')]
    public function politiqueDeConfidentialite(): Response
    {
        return $this->render('frontend/pages/default/politique_de_confidentialite.html.twig');
    }

    #[Route('/equipe-de-dev', name: 'equipe_dev')]
    public function equipeDev(): Response
    {
        return $this->render('frontend/pages/default/equipe_de_dev.html.twig');
    }
}

<?php

namespace App\Frontend\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/panier',name:'panier_')]
class BasketController extends AbstractController
{
    private $productRepository;

    public function __construct( ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    #[Route('/home', name: 'home')]
    public function panier(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('frontend/pages/panier.html.twig',[
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function panierAdd(SessionInterface $session, $id)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);
       $this->addFlash('success', 'Le produit à bien était ajouté au panier');
       return $this->redirectToRoute('view_product',['idProduct' => $id]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function panierRemove(SessionInterface $session, $id): Response
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        $this->addFlash('success', 'Le produit a bien était supprimer de votre panier');
        return $this->redirectToRoute('panier_home');
    }


}

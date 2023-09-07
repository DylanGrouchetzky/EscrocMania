<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(ProductRepository $productRepository, SessionInterface $session): Response
    {
      \Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
      // Les éléments du panier
      $panier = $session->get('panier', []);
      $panierWithData = [];
      foreach ($panier as $id => $quantity) {
          
          $product = $productRepository->find($id);
          $panierWithData[] = 
          [
            'price_data' => [
              'currency' => 'eur',
              'product_data' => [
                'name' => $product->getName(),
              ],
              'unit_amount' => $product->getPrice(),
            ],
            'quantity' => $quantity,
          ];
      }

        // Création de la session Stripe
        $session = Session::create([
            'line_items' => $panierWithData,
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/payement-is-succes',
            'cancel_url' => 'http://localhost:8000/'
        ]);
        
        return $this->redirect($session->url, 303);
    }

}

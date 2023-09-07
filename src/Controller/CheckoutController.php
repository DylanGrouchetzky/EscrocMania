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
            'success_url' => 'http://localhost:8000/stripe/paiement/',
            'cancel_url' => 'http://localhost:8000/'
        ]);
        
        return $this->redirect($session->url, 303);
    }

    #[Route('/checkout/create-charge/', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, SessionInterface $session, OrderRepository $orderRepository, MailerInterface $mailer, $step)
    {
      $order = $this->$orderRepository->find($id);
      $order->setStep(1);

      Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
      Stripe\Charge::create ([
        "amount" => 5 * 100,
        "currency" => "usd",
        "source" => $request->request->get('stripeToken'),
        "description" => "Binaryboxtuts Payment Test"
      ]);
      $this->addFlash(
          'success',
          'Le payement a bien était éffectué'
      );
      $email = (new Email())
      ->from('escromania3993@outlook.fr')
      ->to('escromania3993@outlook.fr')
      ->subject('Demande de contact')
      ->html('<p>De : Escromania </p><p>Email: escromania3993@outlook.fr </p><p>Message : <br><br> Merci pour votre commande </p><p>Ce message provient du site <a href="http://127.0.0.1:8000">Escromania</a></p>');

      $mailer->send($email);
      $session->set('panier', []);
      return $this->render('frontend/paiement/valid_basket.html.twig');
    }
}

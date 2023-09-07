<?php

namespace App\Frontend\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Stripe;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class StripeController extends AbstractController
{
    #[Route('/stripe/{step}', name: 'app_stripe')]
    public function index(SessionInterface $session, ProductRepository $productRepository, $step = null): Response
    {
        if ($step != 'paiement') {
            return $this->redirectToRoute('app_stripe_charge');
        }
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('frontend/paiement/card.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'total' => $total,
        ]);
    }
 
 
    #[Route('/payement-is-succes', name: 'app_stripe_charge')]
    public function createCharge(SessionInterface $session, MailerInterface $mailer)
    {
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
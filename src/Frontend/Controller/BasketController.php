<?php

namespace App\Frontend\Controller;

use App\Entity\Order;
use App\Entity\RowOrder;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        return $this->render('frontend/paiement/panier.html.twig',[
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

    #[Route('/resume-basket/{quantityTotal}/{step}', name: 'resum')]
    public function panierResume(SessionInterface $session, EntityManagerInterface $em, $quantityTotal = null, $step = null): Response
    {
        if ($step != 'commande') {
            return $this->redirectToRoute('panier_home');
        }

        $user = $this->getUser();
        $order = new Order;
        $order->setStep(0)
        ->setCreatedAt(new DateTimeImmutable())
        ->setUser($user);

        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $product = $this->productRepository->find($id);
            $panierWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $row = new RowOrder();
            $row->setNameProduct($product->getName())
            ->setDescriptionProduct($product->getDescription())
            ->setQuantity($quantity)
            ->setUnitPrice($product->getPrice())
            ->setProduct($product)
            ->setBasketOrder($order);

            $em->persist($row);
        }
        $em->persist($order);
        $em->flush();
        
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('frontend/paiement/resum_basket.html.twig',[
            'items' => $panierWithData,
            'total' => $total,
            'quantityTotal' => $quantityTotal
        ]);
    }



    #[Route('/add/{id}', name: 'add')]
    public function panierAdd(SessionInterface $session, Request $request, $id)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id] = $panier[$id] + $request->request->get('quantityProductToCart');
        }else{
            $panier[$id] = $request->request->get('quantityProductToCart');
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

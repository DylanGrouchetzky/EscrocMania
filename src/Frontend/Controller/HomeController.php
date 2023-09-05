<?php

namespace App\Frontend\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('frontend/pages/home.html.twig');
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
        if ($slugCategory == 'Jeux') {
            $bg = 'img/bg-game.jpg';
        }elseif ($slugCategory == 'Goodies') {
            $bg = 'img/bg-goodies.jpg';
        }elseif ($slugCategory == 'Poster') {
            $bg = 'img/bg-poster.jpg';
        }else{
            $bg = null;
        }
        return $this->render('frontend/pages/category.html.twig',[
            'category' => $slugCategory,
            'bg' => $bg,
        ]);
    }

    #[Route('/jeux/{slugGame}', name: 'view_game')]
    public function viewGame($slugGame): Response
    {
        return $this->render('frontend/pages/game.html.twig');
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

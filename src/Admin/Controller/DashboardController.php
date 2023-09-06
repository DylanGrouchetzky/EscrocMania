<?php

namespace App\Admin\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\RowOrder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('/admin/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EscrocMania');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Magasin'),
            MenuItem::linkToCrud('Produits', 'fa fa-file-text', Product::class),
            MenuItem::linkToCrud('Catégories', 'fa fa-book', Category::class),
            MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class),

            MenuItem::section('Utilisateurs'),
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class),
            MenuItem::linkToCrud('Commentaires', 'fa fa-comment', Comment::class),

            MenuItem::section('Commandes'),
            MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Order::class),
            MenuItem::linkToCrud('Détails commandes', 'fa fa-shopping-basket', RowOrder::class),
        ];
    }
}

<?php

namespace App\Frontend\Controller;

use App\Repository\TagRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/search',name:'search_')]
class SearchController extends AbstractController
{
    private $tagRepository;
    private $productRepository;
    private $params;

    public function __construct(TagRepository $tagRepository, ProductRepository $productRepository, ContainerBagInterface $params){
        $this->tagRepository = $tagRepository;
        $this->productRepository = $productRepository;
        $this->params = $params;
    }

    #[Route('/global', name: 'global')]
    public function searcProduct(): Response
    {
        $products = $this->productRepository->findBy([],['published_at' => 'ASC'],$this->params->get('limitProductCategory'));
        return $this->render('frontend/pages/search.html.twig',[
            'products' => $products,
        ]);
    }

    #[Route('/next-product-global/{start}/{action}', name: 'view_category_next_product_global')]
    public function nextProduct($start,$action){
        if ($action == 'next') {
            $start = $start + $this->params->get('limitProductCategory');
            $nextProduct = $this->productRepository->findBy([],['published_at' => 'ASC'],1,$start + $this->params->get('limitProductCategory'));
            if (!$nextProduct) {
                $classButtonNext = "disabled";
            }else{
                $classButtonNext = null;
            }
            $classButtonPrev = null;
        }elseif ($action == 'prev') {
            $start = $start - $this->params->get('limitProductCategory');
            if ($start == 0 || $start < 0) {
                $classButtonPrev = "disabled";
            }else{
                $classButtonPrev = null;
            }
            $classButtonNext = null;
        }

        $products = $this->productRepository->findBy([],['published_at' => 'ASC'],$this->params->get('limitProductCategory'),$start);
        
        return $this->render('frontend/_parts/nextProductGlobalSearch.html.twig',[
            'products' => $products,
            'start' => $start,
            'classButtonNext' => $classButtonNext,
            'classButtonPrev' => $classButtonPrev,
        ]);
    }

    

    #[Route('/resultat', name: 'result')]
    public function searcResult(Request $request): Response
    {
        $idCategory = $request->request->get('selectCategory');
        $terms = $request->request->get('termsSearch');
        $products = $this->productRepository->getSearchProduct($idCategory, $terms);

        return $this->render('frontend/pages/search.html.twig',[
            'products' => $products,
            'categorySearch' => $idCategory,
            'terms' => $terms,
        ]);
    }
}

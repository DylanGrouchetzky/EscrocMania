<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension{

    private $categoryRepository;
    private $productRepository;
    private $params;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, ContainerBagInterface $params){
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->params = $params;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("getCategory", [$this, "getCategory"]),
            new TwigFunction("getLastArticle", [$this, "getLastArticle"]),
        ];
    }

    public function getCategory(){
        $categories = $this->categoryRepository->findAll();
        return $categories;
    }

    public function getLastArticle(){
        return $this->productRepository->findBy([], ["published_at" => "DESC"], $this->params->get('numberLastArticle'));
    }
}
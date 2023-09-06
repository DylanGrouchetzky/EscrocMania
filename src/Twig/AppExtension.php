<?php

namespace App\Twig;

use App\Repository\TagRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension{

    private $categoryRepository;
    private $productRepository;
    private $params;
    private $tagRepository;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, ContainerBagInterface $params,TagRepository $tagRepository){
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->params = $params;
        $this->tagRepository = $tagRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("getCategory", [$this, "getCategory"]),
            new TwigFunction("getLastArticle", [$this, "getLastArticle"]),
            new TwigFunction("getTags", [$this, "getTags"]),
        ];
    }

    public function getCategory(){
        $categories = $this->categoryRepository->findAll();
        return $categories;
    }

    public function getLastArticle(){
        return $this->productRepository->findBy([], ["published_at" => "DESC"], $this->params->get('numberLastArticle'));
    }

    public function getTags(){
        return $this->tagRepository->findAll();
    }
}
<?php

namespace App\Twig;

use App\Repository\TagRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension{

    private $categoryRepository;
    private $productRepository;
    private $params;
    private $tagRepository;
    private $commentRepository;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, ContainerBagInterface $params,TagRepository $tagRepository, CommentRepository $commentRepository){
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->params = $params;
        $this->tagRepository = $tagRepository;
        $this->commentRepository = $commentRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("getCategory", [$this, "getCategory"]),
            new TwigFunction("getLastArticle", [$this, "getLastArticle"]),
            new TwigFunction("getDiscountsArticles", [$this, "getDiscountsArticles"]),
            new TwigFunction("getTags", [$this, "getTags"]),
            new TwigFunction("getNoteGeneral", [$this, "getNoteGeneral"]),
            new TwigFunction("numberArticleInBAsket", [$this, "numberArticleInBAsket"]),
        ];
    }

    public function getCategory(){
        $categories = $this->categoryRepository->findAll();
        return $categories;
    }

    public function getLastArticle(){
        return $this->productRepository->findBy([], ["published_at" => "DESC"], $this->params->get('numberLastArticle'));
    }

    public function getDiscountsArticles(){
        return $this->productRepository->getRandomProduct(4);
    }

    public function getTags(){
        return $this->tagRepository->findAll();
    }

    public function getNoteGeneral($idProduct){
        $comment = $this->commentRepository->findBy(['product' => $idProduct]);
        $noteTotal = 0;
        for ($i=0; $i < count($comment); $i++) { 
            $noteTotal = $noteTotal + $comment[$i]->getNote();
        }
        return $noteTotal / count($comment);
    }
}
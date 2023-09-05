<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class AppExtension extends AbstractExtension{

    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository){
        $this->categoryRepository = $categoryRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("getCategory", [$this, "getCategory"]),
        ];
    }

    public function getCategory(){
        $categories = $this->categoryRepository->findAll();

        return $categories;
    }
}
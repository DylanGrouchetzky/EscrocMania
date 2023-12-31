<?php

namespace App\Admin\Controller;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            TextEditorField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),
            IntegerField::new('quantity'),
            DateTimeField::new('created_at'),
            DateTimeField::new('published_at'),
            ImageField::new('tmp')->onlyOnDetail()->hideOnDetail(),
            CollectionField::new('pictures')
            ->setEntryIsComplex()
            ->onlyOnForms()
            ->setTemplatePath('admin/_parts/pictureProduct.html.twig')
            ->useEntryCrudForm(PictureCrudController::class),
            CollectionField::new('pictures')
            ->onlyOnIndex()
            ->setTemplatePath('admin/_parts/pictureProduct.html.twig'),
            AssociationField::new('category')
        ];
    }
    
}

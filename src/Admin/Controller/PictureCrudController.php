<?php

namespace App\Admin\Controller;

use App\Entity\Picture;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            TextField::new('path'),
            TextField::new('slug'),
            // ImageField::new('image')
            // ->setBasePath('img/products/')
            // ->setUploadDir('public/img/products')
            // ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
            // ->setRequired(false),
            AssociationField::new('product', 'Produit')
        ];
    }
}

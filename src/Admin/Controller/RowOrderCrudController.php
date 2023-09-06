<?php

namespace App\Admin\Controller;

use App\Entity\RowOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RowOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RowOrder::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

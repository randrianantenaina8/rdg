<?php

namespace App\Controller\Admin;

use App\Entity\ReuseType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReuseTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReuseType::class;
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

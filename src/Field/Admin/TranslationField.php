<?php                                      
                                                     
namespace App\Field\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class TranslationField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string      $propertyName
     * @param string|null $label
     * @param array       $fieldsConfig
     *
     * @return static
     */
    public static function new(string $propertyName, ?string $label = null, $fieldsConfig = []): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(TranslationsType::class)
            ->setFormTypeOptions(
                [
                    'default_locale' => '%locale%',
                    'fields' => $fieldsConfig,
                ]
            )
            ->addJsFiles('bundles/easyadmin/form-type-slug.js')
            ;
    }
}

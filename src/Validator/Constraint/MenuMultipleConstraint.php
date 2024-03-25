<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\MenuMultipleValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MenuMultipleConstraint extends Constraint
{
    public const MISSING_ALL_TRANS = 'translations.all.missing';
    public const TOO_MANY_LINKS = 'menu.link.toomany';
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';
    public const NO_PARENT_WITH_CHILDREN = 'menu.parent.children';

    /**
     * @return string|string[]
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return MenuMultipleValidator::class;
    }
}

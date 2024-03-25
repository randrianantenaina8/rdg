<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\MenuBasicValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MenuBasicConstraint extends Constraint
{
    public const MISSING_ALL_TRANS = 'translations.all.missing';
    public const MISSING_LINKS = 'menu.link.missing';
    public const TOO_MANY_LINKS = 'menu.link.toomany';
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';

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
        return MenuBasicValidator::class;
    }
}

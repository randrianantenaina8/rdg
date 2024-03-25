<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\LaminaValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LaminaConstraint extends Constraint
{
    public const MISSING_TITLE_IN_ALL_LANGUAGE = 'translations.field.title.missing';
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
        return LaminaValidator::class;
    }
}

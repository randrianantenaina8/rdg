<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\HeadingValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HeadingConstraint extends Constraint
{
    public const MISSING_ALL_TRANS = 'translations.all.missing';
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
        return HeadingValidator::class;
    }
}

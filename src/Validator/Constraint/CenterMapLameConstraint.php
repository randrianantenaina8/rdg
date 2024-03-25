<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\CenterMapLameValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CenterMapLameConstraint extends Constraint
{
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';
    public const ENTITY_FIELD_MIN_LENGTH = 'entity.field.locale.min';
    public const NO_CONTENT_WITHOUT_TITLE = 'entity.field.nocontent';

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
        return CenterMapLameValidator::class;
    }
}

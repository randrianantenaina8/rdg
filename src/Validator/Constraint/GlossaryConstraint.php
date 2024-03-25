<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\GlossaryValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GlossaryConstraint extends Constraint
{
    public const MISSING_FIELDS = 'translations.field.missing';
    public const AT_LEAST_ONE_LOCALE = 'translations.missing';
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
        return GlossaryValidator::class;
    }
}

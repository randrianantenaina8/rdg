<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\DatasetValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DatasetConstraint extends Constraint
{
    public const MISSING_FIELDS = 'translations.field.missing';
    public const AT_LEAST_ONE_LOCALE = 'entity.mandatory.onelocale';
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
        return DatasetValidator::class;
    }
}

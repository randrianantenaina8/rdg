<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\SubjectValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SubjectConstraint extends Constraint
{
    public const AT_LEAST_ONE_LOCALE = 'subject.translation.missing';
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
        return SubjectValidator::class;
    }
}

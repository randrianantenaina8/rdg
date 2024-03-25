<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\HeadingFaqValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HeadingFaqConstraint extends Constraint
{
    public const MISSING_FIELD = 'headingFaq.field.missing';
    public const WEIGHT_BAD_VALUE = 'weight.invalid.value';

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
        return HeadingFaqValidator::class;
    }
}

<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\CategoryGuideValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CategoryGuideConstraint extends Constraint
{
    public const MISSING_FIELD = 'categoryGuide.field.missing';
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
        return CategoryGuideValidator::class;
    }
}

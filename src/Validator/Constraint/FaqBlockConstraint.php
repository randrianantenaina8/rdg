<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\FaqBlockValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FaqBlockConstraint extends Constraint
{
    public const AT_LEAST_ONE_LOCALE = 'translations.field.missing';
    public const MISSING_FIELDS = 'entity.mandatory.onelocale';
    public const MISSING_HEADING = 'faqblock.field.heading.missing';
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
        return FaqBlockValidator::class;
    }
}

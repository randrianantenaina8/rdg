<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\IntroductionValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IntroductionConstraint extends Constraint
{
    public const AT_LEAST_ONE_LOCALE_TITLE = 'translations.field.title.missing';
    public const NO_DESC_WITHOUT_TITLE = 'introduction.field.nodescription';
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
        return IntroductionValidator::class;
    }
}

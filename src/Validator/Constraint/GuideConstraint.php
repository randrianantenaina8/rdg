<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\GuideValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GuideConstraint extends Constraint
{
    public const MISSING_FIELDS = 'translations.field.missing';
    public const AT_LEAST_ONE_LOCALE = 'entity.mandatory.onelocale';
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';
    public const MISSING_CATEGORY = 'guide.field.category.missing';
    public const ADD_HELP_FIELD_MISSING = 'guide.additionalhelp.fields.missing';

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
        return GuideValidator::class;
    }
}

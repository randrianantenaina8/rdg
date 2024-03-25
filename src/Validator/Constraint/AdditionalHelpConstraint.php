<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\AdditionalHelpValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AdditionalHelpConstraint extends Constraint
{
    public const MISSING_FIELDS = 'translations.field.missing';
    public const AT_LEAST_ONE_LOCALE = 'translations.missing';
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';
    public const LINK_TOO_LONG = 'additionalhelp.field.link.toolong';
    public const AT_LEAST_ONE_LINK = 'additionalHelp.links.atleastone';

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
        return AdditionalHelpValidator::class;
    }
}

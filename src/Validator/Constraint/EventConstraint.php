<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\EventValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EventConstraint extends Constraint
{
    public const MISSING_FIELDS = 'translations.field.missing';
    public const AT_LEAST_ONE_LOCALE = 'entity.mandatory.onelocale';
    public const ENTITY_FIELD_MAX_LENGTH = 'entity.field.locale.lengthmax';
    public const END_SOONER_THAN_BEGIN = 'event.field.end.sooner';

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
        return EventValidator::class;
    }
}

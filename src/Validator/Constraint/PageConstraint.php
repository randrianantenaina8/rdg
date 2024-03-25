<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\PageValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PageConstraint extends Constraint
{
    public const MISSING_TRANS = 'translations.missing';
    public const MISSING_TITLE = 'page.missing.title';
    public const MISSING_CONTENT = 'page.missing.content';
    public const MISSING_PROP = 'page.missing.prop';
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
        return PageValidator::class;
    }
}

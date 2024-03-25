<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\UserEditValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserEditConstraint extends Constraint
{
    public const NONUNIQUE_USERNAME = 'username.nonunique';
    public const NONUNIQUE_EMAIL = 'user.email.nonunique';

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
        return UserEditValidator::class;
    }
}

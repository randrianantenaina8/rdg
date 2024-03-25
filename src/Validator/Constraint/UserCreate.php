<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\UserCreateValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserCreate extends Constraint
{
    public const INVALID_USERNAME = 'username.invalid';
    public const NONUNIQUE_USERNAME = 'username.nonunique';
    public const NONUNIQUE_EMAIL = 'user.email.nonunique';
    public const INVALID_ROLE = 'user.roles.invalid';
    public const PASSWORD_MATCH = 'password.match';
    public const PASSWORD_PATTERN = 'password.pattern';

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
        return UserCreateValidator::class;
    }
}

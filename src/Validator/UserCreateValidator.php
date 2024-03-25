<?php                                      
                                                     
namespace App\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserCreateValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    protected $userRepo;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->userRepo = $em->getRepository(User::class);
    }

    /**
     * @param User       $userCreate
     * @param Constraint $constraint
     */
    public function validate($userCreate, Constraint $constraint)
    {
        if (!ctype_alnum($userCreate->getUsername())) {
            $this->context
                ->buildViolation($constraint::INVALID_USERNAME)
                ->atPath('username')
                ->addViolation();
        } elseif ($this->userRepo->findOneBy(['username' => $userCreate->getUsername()])) {
            $this->context
                ->buildViolation($constraint::NONUNIQUE_USERNAME)
                ->atPath('username')
                ->addViolation();
        }
        if ($this->userRepo->findOneBy(['email' => $userCreate->getEmail()])) {
            $this->context
                ->buildViolation($constraint::NONUNIQUE_EMAIL)
                ->atPath('email')
                ->addViolation();
        }
        if (false === $this->checkValidRoles($userCreate->getRoles())) {
            $this->context
                ->buildViolation($constraint::INVALID_ROLE)
                ->atPath('')
                ->addViolation();
        }
        if ($userCreate->getPassword() != $userCreate->getClearPassword()) {
            $this->context
                ->buildViolation($constraint::PASSWORD_MATCH)
                ->atPath('clearPassword')
                ->addViolation();
        }
        if (1 !== preg_match(User::PATTERN_PASSWORD, $userCreate->getClearPassword())) {
            $this->context
                ->buildViolation($constraint::PASSWORD_PATTERN)
                ->atPath('password')
                ->addViolation();
        }
    }

    /**
     * @param array $userRoles
     *
     * @return bool
     */
    protected function checkValidRoles($userRoles)
    {
        if (!is_array($userRoles)) {
            return false;
        }
        foreach ($userRoles as $userRole) {
            if (!is_string($userRole)) {
                return false;
            }
            if (!in_array($userRole, User::ROLES)) {
                return false;
            }
        }
        return true;
    }
}

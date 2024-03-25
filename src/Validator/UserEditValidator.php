<?php                                      
                                                     
namespace App\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserEditValidator extends ConstraintValidator
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
     * @param User       $user
     * @param Constraint $constraint
     */
    public function validate($user, Constraint $constraint)
    {
        if ($this->userRepo->findOneByEmailExcludedItself($user->getId(), $user->getEmail())) {
            $this->context
                ->buildViolation($constraint::NONUNIQUE_EMAIL)
                ->atPath('email')
                ->addViolation();
        }
        if ($this->userRepo->findOneByUsernameExcludedItself($user->getId(), $user->getUsername())) {
            $this->context
                ->buildViolation($constraint::NONUNIQUE_USERNAME)
                ->atPath('username')
                ->addViolation();
        }
    }
}

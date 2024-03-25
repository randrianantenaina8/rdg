<?php                                      
                                                     
namespace App\Validator;

use App\Entity\CenterMapCoord;
use App\Entity\DataWorkshop;
use App\Entity\Institution;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CenterMapCoordValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param CenterMapCoord $centerMapCoord
     * @param Constraint $constraint
     */
    public function validate($centerMapCoord, Constraint $constraint)
    {
        if (!is_numeric($centerMapCoord->getX())) {
            $this->context
                ->buildViolation($constraint::NOT_NUMERIC)
                ->atPath('x')
                ->addViolation();
        }
        if (!is_numeric($centerMapCoord->getY())) {
            $this->context
                ->buildViolation($constraint::NOT_NUMERIC)
                ->atPath('y')
                ->addViolation();
        }
        if (
            $centerMapCoord->getDataworkshop() instanceof DataWorkshop &&
            $centerMapCoord->getInstitution() instanceof Institution
        ) {
            $this->context
                ->buildViolation($constraint::NOT_BOTH)
                ->atPath('dataworkshop')
                ->addViolation();
        }
        if (
            !$centerMapCoord->getDataworkshop() instanceof DataWorkshop &&
            !$centerMapCoord->getInstitution() instanceof Institution
        ) {
            $this->context
                ->buildViolation($constraint::NOT_BOTH_EMPTY)
                ->atPath('institution')
                ->addViolation();
        }
    }
}

<?php                                      
                                                     
namespace App\Validator;

use App\Entity\CategoryGuide;
use App\Entity\CategoryGuideDraft;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryGuideValidator extends ConstraintValidator
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
     * @param CategoryGuide|CategoryGuideDraft $categoryGuide
     * @param Constraint                       $constraint
     */
    public function validate($categoryGuide, Constraint $constraint)
    {
        $fieldsMissing = $this->getMissingField($categoryGuide);
        foreach ($fieldsMissing as $fieldMissing) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELD)
                ->atPath('categoryGuide')
                ->setParameters($fieldMissing)
                ->addViolation();
        }
        if (!is_int($categoryGuide->getWeight())) {
            $this->context
                ->buildViolation($constraint::WEIGHT_BAD_VALUE)
                ->atPath('weight')
                ->addViolation();
        }
    }

    /**
     * @param CategoryGuide|CategoryGuideDraft $categoryGuide
     *
     * @return array
     */
    protected function getMissingField($categoryGuide)
    {
        $missingFields = [];

        if (!$categoryGuide->getCategory()) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('content.category'),
            ];
        }
        if (!$categoryGuide->getGuide()) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('content.guide'),
            ];
        }
        if (is_null($categoryGuide->getWeight())) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('prop.weight'),
            ];
        }

        return $missingFields;
    }
}

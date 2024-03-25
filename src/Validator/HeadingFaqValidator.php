<?php                                      
                                                     
namespace App\Validator;

use App\Entity\HeadingFaq;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class HeadingFaqValidator extends ConstraintValidator
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
     * @param HeadingFaq $headingFaq
     * @param Constraint $constraint
     */
    public function validate($headingFaq, Constraint $constraint)
    {
        $fieldsMissing = $this->getMissingField($headingFaq);
        foreach ($fieldsMissing as $fieldMissing) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELD)
                ->atPath('headingFaq')
                ->setParameters($fieldMissing)
                ->addViolation();
        }
        if (!is_int($headingFaq->getWeight())) {
            $this->context
                ->buildViolation($constraint::WEIGHT_BAD_VALUE)
                ->atPath('weight')
                ->addViolation();
        }
    }

    /**
     * @param HeadingFaq $headingFaq
     *
     * @return array
     */
    protected function getMissingField($headingFaq)
    {
        $missingFields = [];

        if (!$headingFaq->getHeading()) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('content.heading'),
            ];
        }
        if (!$headingFaq->getFaq()) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('content.faq'),
            ];
        }
        if (is_null($headingFaq->getWeight())) {
            $missingFields[] = [
                '%field%' => $this->translator->trans('prop.weight'),
            ];
        }

        return $missingFields;
    }
}

<?php                                      
                                                     
namespace App\Validator;

use App\Entity\FaqBlock;
use App\Entity\FaqBlockTranslation;
use App\Validator\Constraint\HeadingFaqConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class FaqBlockValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param string              $locales
     * @param TranslatorInterface $translator
     */
    public function __construct($locales, TranslatorInterface $translator)
    {
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
        $this->translator = $translator;
    }

    /**
     * @param FaqBlock   $faq
     * @param Constraint $constraint
     */
    public function validate($faq, Constraint $constraint)
    {
        $headingConstraint = new HeadingFaqConstraint();

        if (false === $this->isAtLeastOneCategory($faq)) {
            $this->context
                ->buildViolation($constraint::MISSING_HEADING)
                ->atPath('categories')
                ->addViolation();
        }
        foreach ($faq->getHeadings() as $heading) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('headings')
                ->validate($heading, $headingConstraint);
        }
        if (false === $this->isAtLeastOneLanguage($faq)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($faq)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($faq);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param FaqBlock $faq
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($faq)
    {
        if (!method_exists($faq, 'getTranslations')) {
            return false;
        }
        if (!count($faq->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param FaqBlock $faq
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($faq)
    {
        $isOk = false;

        foreach ($faq->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getTitle()) {
                $nbTranslatedFields++;
            }
            if ($translation->getContent()) {
                $nbTranslatedFields++;
            }

            if (2 === $nbTranslatedFields || 0 === $nbTranslatedFields) {
                $isOk = true;
            } else {
                return false;
            }
        }

        return $isOk;
    }

    /**
     * @param FaqBlock $faq
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($faq)
    {
        $checkLength = [];

        foreach ($faq->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $lenTitle = mb_strlen($title);

            if ($lenTitle > FaqBlockTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('faqblock.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => FaqBlockTranslation::LEN_TITLE,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param FaqBlock $faq
     *
     * @return bool
     */
    protected function isAtLeastOneCategory($faq)
    {
        $isOk = false;

        if (count($faq->getHeadings()) > 0) {
            $isOk = true;
        }
        return $isOk;
    }
}

<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Heading;
use App\Entity\HeadingTranslation;
use App\Validator\Constraint\HeadingFaqConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class HeadingValidator extends ConstraintValidator
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
     * @param Heading    $heading
     * @param Constraint $constraint
     */
    public function validate($heading, Constraint $constraint)
    {
        $headingConstraint = new HeadingFaqConstraint();

        foreach ($heading->getFaqs() as $faq) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('faqs')
                ->validate($faq, $headingConstraint);
        }
        if (false === $this->isFullTranslated($heading)) {
            $this->context
                ->buildViolation($constraint::MISSING_ALL_TRANS)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($heading);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * Check if the entity is translated in all available languages.
     *
     * @param Heading $heading
     *
     * @return bool
     */
    protected function isFullTranslated($heading)
    {
        if (!method_exists($heading, 'getTranslations')) {
            return false;
        }
        if (!count($heading->getTranslations())) {
            return false;
        }
        if (count($heading->getTranslations()) !== count($this->locales)) {
            return false;
        }
        return true;
    }

    /**
     * @param Heading $heading
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($heading)
    {
        $checkLength = [];

        foreach ($heading->getTranslations() as $translation) {
            $name = (string)$translation->getName();
            $lenName = mb_strlen($name);

            if ($lenName > HeadingTranslation::LEN_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('heading.prop.name'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenName,
                    '%lengthmax%' => HeadingTranslation::LEN_NAME,
                ];
            }
        }
        return $checkLength;
    }
}

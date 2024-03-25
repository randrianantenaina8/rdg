<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Glossary;
use App\Entity\GlossaryTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class GlossaryValidator extends ConstraintValidator
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
     * @param Glossary   $glossary
     * @param Constraint $constraint
     */
    public function validate($glossary, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($glossary)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($glossary)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($glossary);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Glossary $glossary
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($glossary)
    {
        if (!method_exists($glossary, 'getTranslations')) {
            return false;
        }
        if (!count($glossary->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Glossary $glossary
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($glossary)
    {
        $isOk = false;

        foreach ($glossary->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getTerm()) {
                $nbTranslatedFields++;
            }
            if ($translation->getDefinition()) {
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
     * @param Glossary $glossary
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($glossary)
    {
        $checkLength = [];

        foreach ($glossary->getTranslations() as $translation) {
            $term = (string)$translation->getTerm();
            $lenTerm = mb_strlen($term);

            if ($lenTerm > GlossaryTranslation::LEN_TERM) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('glossary.prop.term'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTerm,
                    '%lengthmax%' => GlossaryTranslation::LEN_TERM,
                ];
            }
        }
        return $checkLength;
    }
}

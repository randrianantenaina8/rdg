<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Logigram;
use App\Entity\LogigramTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class LogigramValidator extends ConstraintValidator
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
     * @param Logigram   $logigram
     * @param Constraint $constraint
     */
    public function validate($logigram, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($logigram)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($logigram)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($logigram);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Logigram $logigram
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($logigram)
    {
        if (!method_exists($logigram, 'getTranslations')) {
            return false;
        }
        if (!count($logigram->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Logigram $logigram
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($logigram)
    {
        $isOk = false;

        foreach ($logigram->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getTitle()) {
                $nbTranslatedFields++;
            }
            if ($translation->getSubTitle()) {
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
     * @param Logigram $logigram
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($logigram)
    {
        $checkLength = [];

        foreach ($logigram->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $lenTitle = mb_strlen($title);

            if ($lenTitle > LogigramTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('logigram.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => LogigramTranslation::LEN_TITLE,
                ];
            }
        }
        return $checkLength;
    }
}

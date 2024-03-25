<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Institution;
use App\Entity\InstitutionTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class InstitutionValidator extends ConstraintValidator
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
     * @param Institution $institution
     * @param Constraint  $constraint
     */
    public function validate($institution, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($institution)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($institution)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($institution);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Institution $institution
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($institution)
    {
        if (!method_exists($institution, 'getTranslations')) {
            return false;
        }
        if (!count($institution->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Institution $institution
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($institution)
    {
        $isOk = false;

        foreach ($institution->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getAcronym()) {
                $nbTranslatedFields++;
            }
            if ($translation->getExtendedName()) {
                $nbTranslatedFields++;
            }
            if ($translation->getDescription()) {
                $nbTranslatedFields++;
            }

            if (3 === $nbTranslatedFields || 0 === $nbTranslatedFields) {
                $isOk = true;
            } else {
                return false;
            }
        }

        return $isOk;
    }

    /**
     * @param Institution $institution
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($institution)
    {
        $checkLength = [];

        foreach ($institution->getTranslations() as $translation) {
            $acronym = (string)$translation->getAcronym();
            $extName = (string)$translation->getExtendedName();
            $lenAcro = mb_strlen($acronym);
            $lenExtName = mb_strlen($extName);

            if ($lenAcro > InstitutionTranslation::LEN_ACRONYM) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('institution.prop.acronym'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenAcro,
                    '%lengthmax%' => InstitutionTranslation::LEN_ACRONYM,
                ];
            }
            if ($lenExtName > InstitutionTranslation::LEN_EXT_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('institution.prop.extended'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenExtName,
                    '%lengthmax%' => InstitutionTranslation::LEN_EXT_NAME,
                ];
            }
        }
        return $checkLength;
    }
}

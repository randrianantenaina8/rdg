<?php                                      
                                                     
namespace App\Validator;

use App\Entity\DataWorkshop;
use App\Entity\DataWorkshopTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class DataWorkshopValidator extends ConstraintValidator
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
     * @param DataWorkshop $dataworkshop
     * @param Constraint   $constraint
     */
    public function validate($dataworkshop, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($dataworkshop)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($dataworkshop)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($dataworkshop);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param DataWorkshop $dataworkshop
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($dataworkshop)
    {
        if (!method_exists($dataworkshop, 'getTranslations')) {
            return false;
        }
        if (!count($dataworkshop->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param DataWorkshop $dataworkshop
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($dataworkshop)
    {
        $isOk = false;

        foreach ($dataworkshop->getTranslations() as $translation) {
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
     * @param DataWorkshop $dataworkshop
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($dataworkshop)
    {
        $checkLength = [];

        foreach ($dataworkshop->getTranslations() as $translation) {
            $acronym = (string)$translation->getAcronym();
            $extName = (string)$translation->getExtendedName();
            $lenAcro = mb_strlen($acronym);
            $lenExtName = mb_strlen($extName);

            if ($lenAcro > DataWorkshopTranslation::LEN_ACRONYM) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('dataworkshop.prop.acronym'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenAcro,
                    '%lengthmax%' => DataWorkshopTranslation::LEN_ACRONYM,
                ];
            }
            if ($lenExtName > DataWorkshopTranslation::LEN_EXT_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('dataworkshop.prop.extended'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenExtName,
                    '%lengthmax%' => DataWorkshopTranslation::LEN_EXT_NAME,
                ];
            }
        }
        return $checkLength;
    }
}

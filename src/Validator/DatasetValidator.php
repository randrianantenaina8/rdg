<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Dataset;
use App\Entity\DatasetDraft;
use App\Entity\DatasetTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class DatasetValidator extends ConstraintValidator
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
     * @param Dataset|DatasetDraft $dataset
     * @param Constraint $constraint
     */
    public function validate($dataset, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($dataset)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($dataset)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($dataset);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Dataset|DatasetDraft $dataset
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($dataset)
    {
        if (!method_exists($dataset, 'getTranslations')) {
            return false;
        }
        if (!count($dataset->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Dataset|DatasetDraft $dataset
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($dataset)
    {
        $isOk = false;

        foreach ($dataset->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getTitle()) {
                $nbTranslatedFields++;
            }
            if ($translation->getHook()) {
                $nbTranslatedFields++;
            }
            if ($translation->getContent()) {
                $nbTranslatedFields++;
            }
            if ($translation->getSlug()) {
                $nbTranslatedFields++;
            }

            if (4 === $nbTranslatedFields || 0 === $nbTranslatedFields) {
                $isOk = true;
            } else {
                return false;
            }
        }

        return $isOk;
    }

    /**
     * @param Dataset|DatasetDraft $dataset
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($dataset)
    {
        $checkLength = [];

        foreach ($dataset->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $slug = (string)$translation->getSlug();
            $imgLicence = (string)$translation->getimgLicence();
            $imgLegend = (string)$translation->getImgLegend();
            $lenTitle = mb_strlen($title);
            $lenSlug = mb_strlen($slug);
            $lenImgLicence = mb_strlen($imgLicence);
            $lenImgLegend = mb_strlen($imgLegend);

            if ($lenTitle > DatasetTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('dataset.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => DatasetTranslation::LEN_TITLE,
                ];
            }
            if ($lenSlug > DatasetTranslation::LEN_SLUG) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.slug'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSlug,
                    '%lengthmax%' => DatasetTranslation::LEN_SLUG,
                ];
            }
            if ($lenImgLicence > DatasetTranslation::LEN_IMG_LICENCE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.img.licence'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenImgLicence,
                    '%lengthmax%' => DatasetTranslation::LEN_IMG_LICENCE,
                ];
            }
            if ($lenImgLegend > DatasetTranslation::LEN_IMG_LEGEND) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.img.legend'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenImgLegend,
                    '%lengthmax%' => DatasetTranslation::LEN_IMG_LEGEND,
                ];
            }
        }
        return $checkLength;
    }
}

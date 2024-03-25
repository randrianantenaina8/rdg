<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Actuality;
use App\Entity\ActualityDraft;
use App\Entity\ActualityDraftTranslation;
use App\Entity\ActualityTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActualityValidator extends ConstraintValidator
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
     * @param Actuality|ActualityDraft $actuality
     * @param Constraint $constraint
     */
    public function validate($actuality, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($actuality)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($actuality)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($actuality);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }


    /**
     * @param Actuality|ActualityDraft $actuality
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($actuality)
    {
        if (!method_exists($actuality, 'getTranslations')) {
            return false;
        }
        if (!count($actuality->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Actuality|ActualityDraft $actuality
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($actuality)
    {
        $isOk = false;

        /** @var ActualityDraftTranslation $translation */
        foreach ($actuality->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getTitle()) {
                $nbTranslatedFields++;
            }
            if ($translation->getSlug()) {
                $nbTranslatedFields++;
            }
            if ($translation->getContent()) {
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
     * @param Actuality|ActualityDraft $actuality
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($actuality)
    {
        $checkLength = [];

        /** @var ActualityDraftTranslation $translation */
        foreach ($actuality->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $slug = (string)$translation->getSlug();
            $lenTitle = mb_strlen($title);
            $lenSlug = mb_strlen($slug);

            if ($lenTitle > ActualityTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('actuality.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => ActualityTranslation::LEN_TITLE,
                ];
            }
            if ($lenSlug > ActualityTranslation::LEN_SLUG) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.slug'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSlug,
                    '%lengthmax%' => ActualityTranslation::LEN_SLUG,
                ];
            }
        }
        return $checkLength;
    }
}

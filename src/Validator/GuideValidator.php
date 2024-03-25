<?php                                      
                                                     
namespace App\Validator;

use App\Entity\AdditionalHelp;
use App\Entity\Guide;
use App\Entity\GuideTranslation;
use App\Validator\Constraint\CategoryGuideConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class GuideValidator extends ConstraintValidator
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
     * @param Guide      $guide
     * @param Constraint $constraint
     */
    public function validate($guide, Constraint $constraint)
    {
        $categoryConstraint = new CategoryGuideConstraint();

        if (false === $this->isAtLeastOneCategory($guide)) {
            $this->context
                ->buildViolation($constraint::MISSING_CATEGORY)
                ->atPath('categories')
                ->addViolation();
        }
        foreach ($guide->getCategories() as $category) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('categories')
                ->validate($category, $categoryConstraint);
        }
        if (false === $this->isAtLeastOneLanguage($guide)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($guide)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($guide);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
        if (false === $this->checkAdditionalHelps($guide)) {
            $this->context
                ->buildViolation($constraint::ADD_HELP_FIELD_MISSING)
                ->atPath('additionalHelps')
                ->addViolation();
        }
    }

    /**
     * @param Guide $guide
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($guide)
    {
        if (!method_exists($guide, 'getTranslations')) {
            return false;
        }
        if (!count($guide->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Guide $guide
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($guide)
    {
        $isOk = false;

        foreach ($guide->getTranslations() as $translation) {
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
     * @param Guide $guide
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($guide)
    {
        $checkLength = [];

        foreach ($guide->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $slug = (string)$translation->getSlug();
            $lenTitle = mb_strlen($title);
            $lenSlug = mb_strlen($slug);

            if ($lenTitle > GuideTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('guide.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => GuideTranslation::LEN_TITLE,
                ];
            }
            if ($lenSlug > GuideTranslation::LEN_SLUG) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.slug'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSlug,
                    '%lengthmax%' => GuideTranslation::LEN_SLUG,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param Guide $guide
     *
     * @return bool
     */
    protected function isAtLeastOneCategory($guide)
    {
        $isOk = false;

        if (count($guide->getCategories()) > 0) {
            $isOk = true;
        }
        return $isOk;
    }

    /**
     * @param Guide $guide
     */
    protected function checkAdditionalHelps($guide)
    {
        if (count($guide->getAdditionalHelps()) == 0) {
            return true;
        }
        /** @var AdditionalHelp $additionalHelp */
        foreach ($guide->getAdditionalHelps() as $additionalHelp) {
            if (!is_numeric($additionalHelp->getWeight())) {
                return false;
            }
            if (!$additionalHelp->getAdditionalHelp()) {
                return false;
            }
        }
        return true;
    }
}

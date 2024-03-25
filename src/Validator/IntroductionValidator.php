<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Introduction;
use App\Entity\IntroductionTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntroductionValidator extends ConstraintValidator
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
     * @param Introduction $introduction
     * @param Constraint   $constraint
     */
    public function validate($introduction, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($introduction)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE_TITLE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isNoDescriptionWithoutTitle($introduction)) {
            $this->context
                ->buildViolation($constraint::NO_DESC_WITHOUT_TITLE)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($introduction);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Introduction $introduction
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($introduction)
    {
        if (!method_exists($introduction, 'getTranslations')) {
            return false;
        }
        if (!count($introduction->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Introduction $introduction
     */
    protected function isNoDescriptionWithoutTitle($introduction)
    {
        foreach ($introduction->getTranslations() as $translation) {
            if ($translation->getDescription() && !trim($translation->getTitle())) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Introduction $introduction
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($introduction)
    {
        $checkLength = [];

        foreach ($introduction->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $description = (string)$translation->getDescription();
            $lenTitle = mb_strlen($title);
            $lenDesc = mb_strlen($description);

            if ($lenTitle > IntroductionTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('introduction.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => IntroductionTranslation::LEN_TITLE,
                ];
            }
            if ($lenDesc > IntroductionTranslation::LEN_DESC) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('introduction.prop.descr'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenDesc,
                    '%lengthmax%' => IntroductionTranslation::LEN_DESC,
                ];
            }
        }
        return $checkLength;
    }
}

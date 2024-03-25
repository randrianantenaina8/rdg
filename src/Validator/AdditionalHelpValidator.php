<?php                                      
                                                     
namespace App\Validator;

use App\Entity\AdditionalHelp;
use App\Entity\AdditionalHelpTranslation;
use App\Entity\Guide;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdditionalHelpValidator extends ConstraintValidator
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
     * @param AdditionalHelp $additionalHelp
     * @param Constraint     $constraint
     */
    public function validate($additionalHelp, Constraint $constraint)
    {
        if (mb_strlen($additionalHelp->getLink()) > AdditionalHelp::LEN_LINK) {
            $this->context
                ->buildViolation($constraint::LINK_TOO_LONG)
                ->atPath('link')
                ->addViolation();
        }
        if (false === $this->isAtLeastOneLink($additionalHelp)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LINK)
                ->atPath('links')
                ->addViolation();
        }
        if (false === $this->isAtLeastOneLanguage($additionalHelp)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($additionalHelp)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($additionalHelp);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param AdditionalHelp $additionalHelp
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($additionalHelp)
    {
        if (!method_exists($additionalHelp, 'getTranslations')) {
            return false;
        }
        if (!count($additionalHelp->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param AdditionalHelp $additionalHelp
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($additionalHelp)
    {
        $isOk = false;

        foreach ($additionalHelp->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getName()) {
                $nbTranslatedFields++;
            }
            if ($translation->getDescription()) {
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
     * @param AdditionalHelp $additionalHelp
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($additionalHelp)
    {
        $checkLength = [];

        foreach ($additionalHelp->getTranslations() as $translation) {
            $name = (string)$translation->getName();
            $lenName = mb_strlen($name);
            $description = (string)$translation->getDescription();
            $lenDescription = mb_strlen($description);

            if ($lenName > AdditionalHelpTranslation::LEN_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('additionalHelp.prop.name'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenName,
                    '%lengthmax%' => AdditionalHelpTranslation::LEN_NAME,
                ];
            }
            if ($lenDescription > AdditionalHelpTranslation::LEN_DESC) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('additionalHelp.prop.description'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenName,
                    '%lengthmax%' => AdditionalHelpTranslation::LEN_DESC,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param AdditionalHelp $additionalHelp
     */
    protected function isAtLeastOneLink($additionalHelp)
    {
        $guide = $additionalHelp->getGuide();
        $link = $additionalHelp->getLink();

        if (!$guide instanceof Guide && empty($link)) {
            return false;
        }
        if ($guide instanceof Guide && $guide->getId() && mb_strlen($link)) {
            return false;
        }

        return true;
    }
}

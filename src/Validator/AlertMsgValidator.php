<?php                                      
                                                     
namespace App\Validator;

use App\Entity\AlertMsg;
use App\Entity\AlertMsgTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class AlertMsgValidator extends ConstraintValidator
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
     * @param AlertMsg   $alertMsg
     * @param Constraint $constraint
     */
    public function validate($alertMsg, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($alertMsg)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($alertMsg)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($alertMsg);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param AlertMsg $alertMsg
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($alertMsg)
    {
        if (!method_exists($alertMsg, 'getTranslations')) {
            return false;
        }
        if (!count($alertMsg->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param AlertMsg $alertMsg
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($alertMsg)
    {
        $isOk = false;

        foreach ($alertMsg->getTranslations() as $translation) {
            $nbTranslatedFields = 0;
            if ($translation->getName()) {
                $nbTranslatedFields++;
            }
            if ($translation->getMessage()) {
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
     * @param AlertMsg $alertMsg
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($alertMsg)
    {
        $checkLength = [];

        foreach ($alertMsg->getTranslations() as $translation) {
            $name = (string)$translation->getName();
            $lenName = mb_strlen($name);
            $message = $translation->getMessage();
            $lenMessage = mb_strlen($message);

            if ($lenName > AlertMsgTranslation::LEN_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('alert.prop.name'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenName,
                    '%lengthmax%' => AlertMsgTranslation::LEN_NAME,
                ];
            }

            if ($lenMessage > AlertMsgTranslation::LEN_MESSAGE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('alert.prop.message'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenMessage,
                    '%lengthmax%' => AlertMsgTranslation::LEN_MESSAGE,
                ];
            }
        }

        return $checkLength;
    }
}

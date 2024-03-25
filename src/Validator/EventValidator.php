<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Event;
use App\Entity\EventTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventValidator extends ConstraintValidator
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
     * @param Event      $event
     * @param Constraint $constraint
     */
    public function validate($event, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($event)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        if (false === $this->isFullyFilledInOneLanguage($event)) {
            $this->context
                ->buildViolation($constraint::MISSING_FIELDS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($event);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
        if (false === $this->isEndNullOrUpperThanBegin($event)) {
            $this->context
                ->buildViolation($constraint::END_SOONER_THAN_BEGIN)
                ->atPath('end')
                ->addViolation();
        }
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($event)
    {
        if (!method_exists($event, 'getTranslations')) {
            return false;
        }
        if (!count($event->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    protected function isFullyFilledInOneLanguage($event)
    {
        $isOk = false;
        foreach ($event->getTranslations() as $translation) {
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
     * @param Event $event
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($event)
    {
        $checkLength = [];

        foreach ($event->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $hook = (string)$translation->getHook();
            $slug = (string)$translation->getSlug();
            $lenTitle = mb_strlen($title);
            $lenHook = mb_strlen($hook);
            $lenSlug = mb_strlen($slug);

            if ($lenTitle > EventTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('event.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => EventTranslation::LEN_TITLE,
                ];
            }
            if ($lenHook > EventTranslation::LEN_HOOK) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('event.prop.hook'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenHook,
                    '%lengthmax%' => EventTranslation::LEN_HOOK,
                ];
            }
            if ($lenSlug > EventTranslation::LEN_SLUG) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.slug'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSlug,
                    '%lengthmax%' => EventTranslation::LEN_SLUG,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    protected function isEndNullOrUpperThanBegin($event)
    {
        $begin = $event->getBegin();
        $end = $event->getEnd();

        if ($end && $begin > $end) {
            return false;
        }
        return true;
    }
}

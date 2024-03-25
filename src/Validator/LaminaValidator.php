<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Lame\Lame;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validate common rules shared by all entities that extends Lamina abstract class.
 */
class LaminaValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;


    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Lame       $lamina
     * @param Constraint $constraint
     */
    public function validate($lamina, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguageTitle($lamina)) {
            $this->context
                ->buildViolation($constraint::MISSING_TITLE_IN_ALL_LANGUAGE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($lamina);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Lame $lamina
     *
     * @return bool
     */
    protected function isAtLeastOneLanguageTitle($lamina)
    {
        $isTitlePresent = false;

        if (!method_exists($lamina, 'getTranslations')) {
            return false;
        }
        $translations = $lamina->getTranslations();
        if (!count($translations)) {
            return false;
        }
        foreach ($translations as $translation) {
            $title = $translation->getTitle();
            if (!is_null($title) && !empty(trim($title))) {
                return true;
            }
        }
        return $isTitlePresent;
    }

    /**
     * @param $lamina
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($lamina)
    {
        $checkLength = [];

        foreach ($lamina->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $lenTitle = mb_strlen($title);

            if ($lenTitle > Lame::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('lame.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => Lame::LEN_TITLE,
                ];
            }
        }
        return $checkLength;
    }
}

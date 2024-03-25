<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Lame\CenterMapLame;
use App\Entity\Lame\CenterMapLameTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CenterMapLameValidator extends ConstraintValidator
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
     * @param CenterMapLame $centerMapLamina
     * @param Constraint    $constraint
     */
    public function validate($centerMapLamina, Constraint $constraint)
    {
        if (false === $this->isNotContentWithoutTitle($centerMapLamina)) {
            $this->context
                ->buildViolation($constraint::NO_CONTENT_WITHOUT_TITLE)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($centerMapLamina);

        foreach ($lengthTranslatedFields['min'] as $itemForMin) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MIN_LENGTH)
                ->atPath('translations')
                ->setParameters($itemForMin)
                ->addViolation();
        }
        foreach ($lengthTranslatedFields['max'] as $itemForMax) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($itemForMax)
                ->addViolation();
        }
    }

    /**
     * Check length's translated fields.
     * Title is not check here because it is a common property of all laminas. So checked by LaminaValidator.
     *
     * @param CenterMapLame $lamina
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($lamina)
    {
        $checkLength = [
            'min' => [],
            'max' => [],
        ];

        foreach ($lamina->getTranslations() as $translation) {
            $content = (string)$translation->getContent();
            $lenContent = mb_strlen($content);

            if ($lenContent == 0) {
                $checkLength['min'][] = [
                    '%field%' => $this->translator->trans('lame.prop.content'),
                    '%lng%' => $translation->getLocale(),
                    '%len%' => CenterMapLameTranslation::LEN_CONTENT_MIN,
                ];
            } elseif ($lenContent > CenterMapLameTranslation::LEN_CONTENT_MAX) {
                $checkLength['max'][] = [
                    '%field%' => $this->translator->trans('lame.prop.content'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenContent,
                    '%lengthmax%' => CenterMapLameTranslation::LEN_CONTENT_MAX,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param CenterMapLame $lamina
     *
     * @return bool
     */
    protected function isNotContentWithoutTitle($lamina)
    {
        foreach ($lamina->getTranslations() as $translation) {
            $title = trim((string)$translation->getTitle());
            $content = trim((string)$translation->getContent());

            if (mb_strlen($content) > 0 && mb_strlen($title) == 0) {
                return false;
            }
        }
        return true;
    }
}

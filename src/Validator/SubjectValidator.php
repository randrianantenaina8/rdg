<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Subject;
use App\Entity\SubjectTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubjectValidator extends ConstraintValidator
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
     * @param Subject    $subject
     * @param Constraint $constraint
     */
    public function validate($subject, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneLanguage($subject)) {
            $this->context
                ->buildViolation($constraint::AT_LEAST_ONE_LOCALE)
                ->atPath('name')
                ->addViolation();
            return;
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($subject);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * @param Subject $faq
     *
     * @return bool
     */
    protected function isAtLeastOneLanguage($subject)
    {
        if (!method_exists($subject, 'getTranslations')) {
            return false;
        }
        if (!count($subject->getTranslations())) {
            return false;
        }
        return true;
    }

    /**
     * @param Subject $subject
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($subject)
    {
        $checkLength = [];

        foreach ($subject->getTranslations() as $translation) {
            $subjectProperty = (string)$translation->getSubject();
            $lenSubject = mb_strlen($subjectProperty);

            if ($lenSubject > SubjectTranslation::LEN_SUBJECT) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('contact.subject.prop.subject'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSubject,
                    '%lengthmax%' => SubjectTranslation::LEN_SUBJECT,
                ];
            }
        }
        return $checkLength;
    }
}

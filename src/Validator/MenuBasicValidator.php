<?php                                      
                                                     
namespace App\Validator;

use App\Entity\MenuBasic;
use App\Entity\MenuBasicTranslation;
use App\Entity\Page;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBasicValidator extends ConstraintValidator
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
     * @param MenuBasic  $menuBasic
     * @param Constraint $constraint
     */
    public function validate($menuBasic, Constraint $constraint)
    {
        $external = $menuBasic->getExternalLink();
        $system = $menuBasic->getSystemLink();
        $page = $menuBasic->getPageLink();
        $nbLink = $this->countLinks($external, $system, $page);

        if (false === $this->isFullTranslated($menuBasic)) {
            $this->context
                ->buildViolation($constraint::MISSING_ALL_TRANS)
                ->atPath('translations')
                ->addViolation();
        }
        if (0 === $nbLink) {
            $this->context
                ->buildViolation($constraint::MISSING_LINKS)
                ->atPath('externalLink')
                ->addViolation();
        } elseif ($nbLink > 1) {
            $this->context
                ->buildViolation($constraint::TOO_MANY_LINKS)
                ->atPath('fakeLink')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($menuBasic);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * Check if the entity is translated in all available languages.
     *
     * @param MenuBasic $entity
     *
     * @return bool
     */
    protected function isFullTranslated($entity)
    {
        if (!method_exists($entity, 'getTranslations')) {
            return false;
        }
        if (!count($entity->getTranslations())) {
            return false;
        }
        if (count($entity->getTranslations()) !== count($this->locales)) {
            return false;
        }
        return true;
    }

    /**
     * Count how many links has been given.
     *
     * @param string|null $external
     * @param string|null $system
     * @param Page|null   $page
     *
     * @return int
     */
    protected function countLinks($external, $system, $page)
    {
        $nbLink = 0;

        if (!empty($external)) {
            $nbLink++;
        }
        if (!empty($system)) {
            $nbLink++;
        }
        if (!empty($page)) {
            $nbLink++;
        }
        return $nbLink;
    }

    /**
     * @param MenuBasic $menuBasic
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($menuBasic)
    {
        $checkLength = [];

        foreach ($menuBasic->getTranslations() as $translation) {
            $label = (string)$translation->getLabel();
            $lenLabel = mb_strlen($label);

            if ($lenLabel > MenuBasicTranslation::LEN_LABEL) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('menuBasic.prop.label'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenLabel,
                    '%lengthmax%' => MenuBasicTranslation::LEN_LABEL,
                ];
            }
        }
        return $checkLength;
    }
}

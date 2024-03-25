<?php                                      
                                                     
namespace App\Validator;

use App\Entity\MenuMultiple;
use App\Entity\MenuMultipleTranslation;
use App\Entity\Page;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuMultipleValidator extends ConstraintValidator
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
     * @param MenuMultiple $menuMultiple
     * @param Constraint   $constraint
     */
    public function validate($menuMultiple, Constraint $constraint)
    {
        $external = $menuMultiple->getExternalLink();
        $system = $menuMultiple->getSystemLink();
        $page = $menuMultiple->getPageLink();
        $nbLink = $this->countLinks($external, $system, $page);

        if (false === $this->isFullTranslated($menuMultiple)) {
            $this->context
                ->buildViolation($constraint::MISSING_ALL_TRANS)
                ->atPath('translations')
                ->addViolation();
        }
        if ($nbLink > 1) {
            $this->context
                ->buildViolation($constraint::TOO_MANY_LINKS)
                ->atPath('fakeLink')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($menuMultiple);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
        if (false === $this->checkParentChildren($menuMultiple)) {
            $this->context
                ->buildViolation($constraint::NO_PARENT_WITH_CHILDREN)
                ->atPath('parent')
                ->addViolation();
        }
    }

    /**
     * Check if the entity is translated in all available languages.
     *
     * @param MenuMultiple $entity
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
     * @param MenuMultiple $menuMultiple
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($menuMultiple)
    {
        $checkLength = [];

        foreach ($menuMultiple->getTranslations() as $translation) {
            $label = (string)$translation->getLabel();
            $lenLabel = mb_strlen($label);

            if ($lenLabel > MenuMultipleTranslation::LEN_LABEL) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('menuBasic.prop.label'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenLabel,
                    '%lengthmax%' => MenuMultipleTranslation::LEN_LABEL,
                ];
            }
        }
        return $checkLength;
    }

    /**
     * @param MenuMultiple $menuMultiple
     *
     * @return bool
     */
    protected function checkParentChildren($menuMultiple)
    {
        if ($menuMultiple->getParent() && count($menuMultiple->getChilds())) {
            return false;
        }
        if ($menuMultiple->getParent() && $menuMultiple->getParent()->getParent()) {
            return false;
        }

        return true;
    }
}

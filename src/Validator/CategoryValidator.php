<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Validator\Constraint\CategoryGuideConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryValidator extends ConstraintValidator
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
     * @param Category   $category
     * @param Constraint $constraint
     */
    public function validate($category, Constraint $constraint)
    {
        $categoryConstraint = new CategoryGuideConstraint();

        foreach ($category->getGuides() as $guide) {
            $this->context->getValidator()
                ->inContext($this->context)
                ->atPath('guides')
                ->validate($guide, $categoryConstraint);
        }
        if (false === $this->isFullTranslated($category)) {
            $this->context
                ->buildViolation($constraint::MISSING_ALL_TRANS)
                ->atPath('translations')
                ->addViolation();
            return;
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($category);
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
     * @param Category $category
     *
     * @return bool
     */
    protected function isFullTranslated($category)
    {
        if (!method_exists($category, 'getTranslations')) {
            return false;
        }
        if (!count($category->getTranslations())) {
            return false;
        }
        if (count($category->getTranslations()) !== count($this->locales)) {
            return false;
        }
        return true;
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($category)
    {
        $checkLength = [];

        foreach ($category->getTranslations() as $translation) {
            $name = (string)$translation->getName();
            $lenName = mb_strlen($name);

            if ($lenName > CategoryTranslation::LEN_NAME) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('category.prop.name'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenName,
                    '%lengthmax%' => CategoryTranslation::LEN_NAME,
                ];
            }
        }
        return $checkLength;
    }
}

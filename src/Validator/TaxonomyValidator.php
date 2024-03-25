<?php                                      
                                                     
namespace App\Validator;

use App\Entity\Taxonomy;
use App\Entity\TaxonomyTranslation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaxonomyValidator extends ConstraintValidator
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
     * @param Taxonomy   $taxo
     * @param Constraint $constraint
     */
    public function validate($taxo, Constraint $constraint)
    {
        if (false === $this->isFullTranslated($taxo)) {
            $this->context
                ->buildViolation($constraint::MISSING_ALL_TRANS)
                ->atPath('translations')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($taxo);
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
     * @param Taxonomy $entity
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
     * @param Taxonomy $taxo
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($taxo)
    {
        $checkLength = [];

        foreach ($taxo->getTranslations() as $translation) {
            $term = (string)$translation->getTerm();
            $lenTerm = mb_strlen($term);

            if ($lenTerm > TaxonomyTranslation::LEN_TERM) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('taxonomy.prop.term'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTerm,
                    '%lengthmax%' => TaxonomyTranslation::LEN_TERM,
                ];
            }
        }
        return $checkLength;
    }
}

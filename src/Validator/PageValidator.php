<?php                                      
                                                     
namespace App\Validator;

use App\Entity\PageDraft;
use App\Entity\PageTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Valid page's translated fields.
 */
class PageValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    protected $repo;

    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(PageDraft::class);
        $this->translations = new ArrayCollection();
        $this->translator = $translator;
    }

    /**
     * @param PageDraft  $page
     * @param Constraint $constraint
     */
    public function validate($page, Constraint $constraint)
    {
        if (false === $this->isAtLeastOneTranslation($page)) {
            $this->context
                ->buildViolation($constraint::MISSING_TRANS)
                ->atPath('title')
                ->addViolation();
            // Return because no need to check others... No translations...
            return;
        }
        if (false === $this->isAtLeatOneTitle()) {
            $this->context
                ->buildViolation($constraint::MISSING_TITLE)
                ->atPath('title')
                ->addViolation();
        }
        if (false === $this->isAtLeastOneContent()) {
            $this->context
                ->buildViolation($constraint::MISSING_CONTENT)
                ->atPath('content')
                ->addViolation();
            // Return because next needs translations.
            return;
        }
        if (false === $this->isTitleAndContentInSameLanguage()) {
            $this->context
                ->buildViolation($constraint::MISSING_PROP)
                ->atPath('title')
                ->addViolation();
        }
        $lengthTranslatedFields = $this->isLengthTranslatedFieldOk($page);
        foreach ($lengthTranslatedFields as $item) {
            $this->context
                ->buildViolation($constraint::ENTITY_FIELD_MAX_LENGTH)
                ->atPath('translations')
                ->setParameters($item)
                ->addViolation();
        }
    }

    /**
     * If no translations at all, not good.
     *
     * @param PageDraft $entity
     *
     * @return bool
     */
    protected function isAtLeastOneTranslation($entity)
    {
        if (!method_exists($entity, 'getTranslations')) {
            return false;
        }
        if (!count($entity->getTranslations())) {
            return false;
        }
        $this->translations = $entity->getTranslations();

        return true;
    }

    /**
     * Check if there is at least one title.
     *
     * @return bool
     */
    protected function isAtLeatOneTitle()
    {
        foreach ($this->translations as $translation) {
            $title = $translation->getTitle();
            if (is_string($title) && mb_strlen(trim($title))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there is at least one content.
     *
     * @return bool
     */
    protected function isAtLeastOneContent()
    {
        foreach ($this->translations as $translation) {
            $content = $translation->getContent();
            if (is_string($content) && mb_strlen(trim($content))) {
                return true;
            }
        }

        return false;
    }

    /**
     * If a title exist in a language, content should be too.
     * And vice versa.
     *
     * @return bool
     */
    protected function isTitleAndContentInSameLanguage()
    {
        foreach ($this->translations as $translation) {
            $lenTitle = mb_strlen(trim($translation->getTitle()));
            $lenContent = mb_strlen(trim($translation->getContent()));

            if ($lenTitle && !$lenContent) {
                return false;
            }
            if ($lenContent && !$lenTitle) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param PageDraft $page
     *
     * @return array
     */
    protected function isLengthTranslatedFieldOk($page)
    {
        $checkLength = [];

        foreach ($page->getTranslations() as $translation) {
            $title = (string)$translation->getTitle();
            $slug = (string)$translation->getSlug();
            $lenTitle = mb_strlen($title);
            $lenSlug = mb_strlen($slug);

            if ($lenTitle > PageTranslation::LEN_TITLE) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('page.prop.title'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenTitle,
                    '%lengthmax%' => PageTranslation::LEN_TITLE,
                ];
            }
            if ($lenSlug > PageTranslation::LEN_SLUG) {
                $checkLength[] = [
                    '%field%' => $this->translator->trans('prop.slug'),
                    '%lng%' => $translation->getLocale(),
                    '%len%'  => $lenSlug,
                    '%lengthmax%' => PageTranslation::LEN_SLUG,
                ];
            }
        }

        return $checkLength;
    }
}

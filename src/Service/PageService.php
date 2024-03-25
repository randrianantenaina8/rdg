<?php                                      
                                                     
namespace App\Service;

use App\Entity\Page;
use App\Entity\PageDraft;
use App\Entity\PageDraftTranslation;
use App\Entity\PageTranslation;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PageService
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \DateTime
     */
    protected $now;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @param string                 $locales
     * @param Security               $security
     * @param EntityManagerInterface $em
     */
    public function __construct($locales, Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
        $this->now = DateTool::dateAndTimeNow();
    }

    /**
     * Publish a PageDraft ie create/update a Page associated to the PageDraft object.
     * Deleting the draft when it is published.
     *
     * @param PageDraft $draft
     */
    public function publish(PageDraft $draft)
    {
        if ($draft->getPage()) {
            $this->updatePage($draft);
            $this->deleteDraft($draft);
            return;
        }
        $this->createPage($draft);
        $this->deleteDraft($draft);
    }

    /**
     * Update Page properties with PageDraft properties.
     *
     * @param PageDraft $draft
     */
    public function updatePage(PageDraft $draft)
    {
        $page = $draft->getPage();
        $page->setUpdatedAt($this->now);
        $page->setUpdatedBy($this->security->getUser());
        /** @var PageTranslation $translation */
        foreach ($page->getTranslations() as $translation) {
            $page->removeTranslation($translation);
            $this->em->remove($translation);
        }
        // Execute actual translations removing from DB.
        $this->em->flush();

        /** @var PageDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $pageTranslation = new PageTranslation();
            $pageTranslation->setLocale($draftTranslation->getLocale());
            $pageTranslation->setTitle($draftTranslation->getTitle());
            $pageTranslation->setContent($draftTranslation->getContent());
            $pageTranslation->setSlug($draftTranslation->getSlug());

            $page->addTranslation($pageTranslation);
        }
        // Execute new translation added to DB.
        $this->em->flush();
    }

    /**
     * Create a Page from a PageDraft then associated it to the PageDraft object.
     *
     * @param PageDraft $draft
     */
    public function createPage(PageDraft $draft)
    {
        $page = new Page();

        $page->setUpdatedBy($this->security->getUser());
        $page->setCreatedBy($this->security->getUser());
        $page->setCreatedAt($this->now);
        $page->setUpdatedAt($this->now);
        /** @var PageDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new PageTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setSlug($draftTranslation->getSlug());

            $page->addTranslation($translation);
        }
        $draft->setPage($page);

        $this->em->persist($page);
        $this->em->flush();
    }

    /**
     * Deleting the draft when it is published.
     *
     * @param PageDraft $draft
     */
    public function deleteDraft(PageDraft $draft)
    {
        $this->em->remove($draft);
        $this->em->flush();
    }

    /**
     * Find a PageDraft object associated to a Page or create a new one from Page object properties.
     *
     * @param Page $page
     *
     * @return PageDraft
     */
    public function findOrCreateDraft(Page $page)
    {
        $pageDraft = $this->em->getRepository(PageDraft::class)->findOneCompleteByPage($page);

        if (!$pageDraft instanceof PageDraft) {
            $pageDraft = new PageDraft();

            $pageDraft->setCreatedBy($this->security->getUser());
            $pageDraft->setUpdatedBy($this->security->getUser());
            $pageDraft->setCreatedAt($this->now);
            $pageDraft->setUpdatedAt($this->now);
            $pageDraft->setPage($page);

            /** @var PageTranslation $pageTranslation */
            foreach ($page->getTranslations() as $pageTranslation) {
                $translation = new PageDraftTranslation();
                $translation->setLocale($pageTranslation->getLocale());
                $translation->setTitle($pageTranslation->getTitle());
                $translation->setContent($pageTranslation->getContent());
                $translation->setSlug($pageTranslation->getSlug());

                $pageDraft->addTranslation($translation);
            }

            $this->em->persist($pageDraft);
            $this->em->flush();
        }

        return $pageDraft;
    }

    /**
     * Duplicate a page
     *
     * @param PageDraft  $originalDraft
     * @return PageDraft
     */
    public function duplicate(PageDraft $originalDraft)
    {
        $clonedDraft = clone $originalDraft;

        $clonedDraft->setUpdatedBy($this->security->getUser());
        $clonedDraft->setCreatedBy($this->security->getUser());
        $clonedDraft->setCreatedAt($this->now);
        $clonedDraft->setUpdatedAt($this->now);
        $clonedDraft->setPage(null);

        /** @var PageTranslation $originalDraftTranslation */
        foreach ($originalDraft->getTranslations() as $originalDraftTranslation) {
            $translation = new PageDraftTranslation();
            $translation->setLocale($originalDraftTranslation->getLocale());
            $translation->setTitle($originalDraftTranslation->getTitle() . '_Copy');
            $translation->setContent($originalDraftTranslation->getContent());
            $translation->setSlug($originalDraftTranslation->getSlug());

            $clonedDraft->addTranslation($translation);
        }

        // Save the cloned item to the database
        $this->em->persist($clonedDraft);
        $this->em->flush();

        return $clonedDraft;
    }
}

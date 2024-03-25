<?php                                      
                                                     
namespace App\Service;

use App\Entity\AdditionalHelpGuide;
use App\Entity\AdditionalHelpGuideDraft;
use App\Entity\CategoryGuide;
use App\Entity\CategoryGuideDraft;
use App\Entity\Guide;
use App\Entity\GuideDraft;
use App\Entity\GuideDraftTranslation;
use App\Entity\GuideTranslation;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GuideService
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

    public function publish(GuideDraft $draft)
    {
        if ($draft->getGuide()) {
            $this->updateGuide($draft);
            $this->deleteDraft($draft);
            return;
        }
        $this->createGuide($draft);
        $this->deleteDraft($draft);
    }

    /**
     * Update Guide properties with GuideDraft properties.
     *
     * @param GuideDraft $draft
     */
    public function updateGuide(GuideDraft $draft)
    {
        $guide = $draft->getGuide();
        $guide->setUpdatedAt($this->now);
        $guide->setUpdatedBy($this->security->getUser());

        // Remove Translations, AdditionnalHelps and Categories collections.
        /** @var GuideTranslation $translation */
        foreach ($guide->getTranslations() as $translation) {
            $guide->removeTranslation($translation);
            $this->em->remove($translation);
        }
        foreach ($guide->getCategories() as $category) {
            $guide->removeCategory($category);
            $this->em->remove($category);
        }
        foreach ($guide->getAdditionalHelps() as $additionalHelp) {
            $guide->removeAdditionalHelp($additionalHelp);
            $this->em->remove($additionalHelp);
        }
        // Execute actual translations, additionnalHelps and Categories removing from DB.
        $this->em->flush();
        // Added new collection of translations, AdditionnalHelps and Categories.
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new GuideTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setSlug($draftTranslation->getSlug());
            $translation->setImageLocale($draftTranslation->getImageLocale());
            $translation->setImgLicence($draftTranslation->getImgLicence());
            $translation->setImgLegend($draftTranslation->getImgLegend());

            $guide->addTranslation($translation);
        }
        foreach ($draft->getAdditionalHelps() as $draftAdditionalHelp) {
            $additionalHelpGuide = new AdditionalHelpGuide();
            $additionalHelpGuide->setGuide($guide);
            $additionalHelpGuide->setWeight($draftAdditionalHelp->getWeight());
            $additionalHelpGuide->setAdditionalHelp($draftAdditionalHelp->getAdditionalHelp());

            $guide->addAdditionalHelp($additionalHelpGuide);
        }
        foreach ($draft->getCategories() as $draftCategory) {
            $categoryGuide = new CategoryGuide();
            $categoryGuide->setGuide($guide);
            $categoryGuide->setWeight($draftCategory->getWeight());
            $categoryGuide->setCategory($draftCategory->getCategory());

            $guide->addCategory($categoryGuide);
        }

        // Save last modifications.
        $this->em->flush();
    }

    /**
     * Create a Guide from a GuideDraft then associated it to the GuideDraft object.
     *
     * @param GuideDraft $draft
     */
    public function createGuide(GuideDraft $draft)
    {
        $guide = new Guide();
        $guide->setCreatedAt($this->now);
        $guide->setUpdatedAt($this->now);
        $guide->setCreatedBy($this->security->getUser());
        $guide->setUpdatedBy($this->security->getUser());

        // Add new collection of translations.
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new GuideTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setSlug($draftTranslation->getSlug());
            $translation->setImageLocale($draftTranslation->getImageLocale());
            $translation->setImgLicence($draftTranslation->getImgLicence());
            $translation->setImgLegend($draftTranslation->getImgLegend());

            $guide->addTranslation($translation);
        }
        // Add new collection of AdditionalHelpGuide
        /** @var AdditionalHelpGuideDraft $draftAdditionalHelp */
        foreach ($draft->getAdditionalHelps() as $draftAdditionalHelp) {
            $additionnalHelpGuide = new AdditionalHelpGuide();
            $additionnalHelpGuide->setGuide($guide);
            $additionnalHelpGuide->setWeight($draftAdditionalHelp->getWeight());
            $additionnalHelpGuide->setAdditionalHelp($draftAdditionalHelp->getAdditionalHelp());

            $guide->addAdditionalHelp($additionnalHelpGuide);
        }
        // Add new collection of CategoryGuide
        foreach ($draft->getCategories() as $draftCategory) {
            $categoryGuide = new CategoryGuide();
            $categoryGuide->setGuide($guide);
            $categoryGuide->setWeight($draftCategory->getWeight());
            $categoryGuide->setCategory($draftCategory->getCategory());

            $guide->addCategory($categoryGuide);
        }

        $draft->setGuide($guide);
        $this->em->persist($guide);
        $this->em->flush();
    }

    /**
     * Deleting the draft when it is published.
     *
     * @param GuideDraft $draft
     */
    public function deleteDraft(GuideDraft $draft)
    {
        $this->em->remove($draft);
        $this->em->flush();
    }

    /**
     * Find a GuideDraft associated to a Guide or create a new one from Guide object.
     *
     * @param Guide $guide
     *
     * @return GuideDraft|mixed
     */
    public function findOrCreateDraft(Guide $guide)
    {
        $guideDraft = $this->em->getRepository(GuideDraft::class)->findOneCompleteByGuide($guide);

        if (!$guideDraft instanceof GuideDraft) {
            $guideDraft = new GuideDraft();

            $guideDraft->setCreatedAt($this->now);
            $guideDraft->setCreatedBy($this->security->getUser());
            $guideDraft->setUpdatedAt($this->now);
            $guideDraft->setUpdatedBy($this->security->getUser());
            $guideDraft->setGuide($guide);

            /** @var GuideTranslation $guideTranslation */
            foreach ($guide->getTranslations() as $guideTranslation) {
                $translation = new GuideDraftTranslation();
                $translation->setLocale($guideTranslation->getLocale());
                $translation->setTitle($guideTranslation->getTitle());
                $translation->setContent($guideTranslation->getContent());
                $translation->setSlug($guideTranslation->getSlug());
                $translation->setImageLocale($guideTranslation->getImageLocale());
                $translation->setImgLicence($guideTranslation->getImgLicence());
                $translation->setImgLegend($guideTranslation->getImgLegend());

                $guideDraft->addTranslation($translation);
            }
            // Add new collection of AdditionalHelpGuideDraft
            /** @var AdditionalHelpGuideDraft $draftAdditionalHelp */
            foreach ($guide->getAdditionalHelps() as $guideAdditionalHelp) {
                $additionnalHelpGuideDraft = new AdditionalHelpGuideDraft();
                $additionnalHelpGuideDraft->setGuide($guideDraft);
                $additionnalHelpGuideDraft->setWeight($guideAdditionalHelp->getWeight());
                $additionnalHelpGuideDraft->setAdditionalHelp($guideAdditionalHelp->getAdditionalHelp());

                $guideDraft->addAdditionalHelp($additionnalHelpGuideDraft);
            }
            // Add new collection of CategoryGuideDraft
            foreach ($guide->getCategories() as $guideCategory) {
                $categoryGuideDraft = new CategoryGuideDraft();
                $categoryGuideDraft->setGuide($guideDraft);
                $categoryGuideDraft->setWeight($guideCategory->getWeight());
                $categoryGuideDraft->setCategory($guideCategory->getCategory());

                $guideDraft->addCategory($categoryGuideDraft);
            }

            $this->em->persist($guideDraft);
            $this->em->flush();
        }

        return $guideDraft;
    }

     /**
     * Duplicate a draft guide
     *
     * @param  GuideDraft $originalDraft
     * @return GuideDraft
     */
    public function duplicate(GuideDraft $originalDraft)
    {
        $clonedDraft = clone $originalDraft;

        $clonedDraft->setUpdatedBy($this->security->getUser());
        $clonedDraft->setCreatedBy($this->security->getUser());
        $clonedDraft->setCreatedAt($this->now);
        $clonedDraft->setUpdatedAt($this->now);
        $clonedDraft->setGuide(null);

        /** @var GuideTranslation $originalDraftTranslation */
        foreach ($originalDraft->getTranslations() as $originalDraftTranslation) {
            $translation = new GuideDraftTranslation();
            $translation->setLocale($originalDraftTranslation->getLocale());
            $translation->setTitle($originalDraftTranslation->getTitle() . '_Copy');
            $translation->setContent($originalDraftTranslation->getContent());
            $translation->setSlug($originalDraftTranslation->getSlug());
            $translation->setImageLocale($originalDraftTranslation->getImageLocale());
            $translation->setImgLicence($originalDraftTranslation->getImgLicence());
            $translation->setImgLegend($originalDraftTranslation->getImgLegend());

            $clonedDraft->addTranslation($translation);
        }

        // Add new collection of CategoryGuideDraft
        foreach ($originalDraft->getCategories() as $originalDraft) {
            $originalCategoryDraft = new CategoryGuideDraft();
            $originalCategoryDraft->setGuide($clonedDraft);
            $originalCategoryDraft->setWeight($originalDraft->getWeight());
            $originalCategoryDraft->setCategory($originalDraft->getCategory());

            $clonedDraft->addCategory($originalCategoryDraft);
        }

        // Save the cloned item to the database
        $this->em->persist($clonedDraft);
        $this->em->flush();

        return $clonedDraft;
    }
}

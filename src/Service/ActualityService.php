<?php                                      
                                                     
namespace App\Service;

use App\Entity\Actuality;
use App\Entity\ActualityDraft;
use App\Entity\ActualityDraftTranslation;
use App\Entity\ActualityTranslation;
use App\Entity\Taxonomy;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ActualityService
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
    public function __construct(
        $locales,
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
        $this->now = DateTool::dateAndTimeNow();
    }

    /**
     * Publish an ActualityDraft ie create/update an Actuality associated to the ActualityDraft object.
     *
     * @param ActualityDraft $draft
     */
    public function publish(ActualityDraft $draft)
    {
        if ($draft->getActuality()) {
            $this->updateActuality($draft);
            $this->deleteDraft($draft);
            return;
        }
        $this->createActuality($draft);
        $this->deleteDraft($draft);
    }

    /**
     * Update Actuality properties with ActualityDraft properties.
     *
     * @param ActualityDraft $draft
     */
    public function updateActuality(ActualityDraft $draft)
    {
        $actuality = $draft->getActuality();
        if (!empty($draft->getPublishedAt())) {
            $actuality->setPublishedAt($draft->getPublishedAt());
        } else {
            $actuality->setPublishedAt($this->now);
        }

        $actuality->setUpdatedAt($this->now);
        $actuality->setUpdatedBy($this->security->getUser());
        /** @var ActualityTranslation $translation */
        foreach ($actuality->getTranslations() as $translation) {
            $actuality->removeTranslation($translation);
            $this->em->remove($translation);
        }
        foreach ($actuality->getTaxonomies() as $taxonomy) {
            $actuality->removeTaxonomy($taxonomy);
            $this->em->remove($taxonomy);
        }
        // Execute actual translations and taxonomies removing from DB.
        $this->em->flush();

        /** @var ActualityDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $actualityTranslation = new ActualityTranslation();
            $actualityTranslation->setLocale($draftTranslation->getLocale());
            $actualityTranslation->setTitle($draftTranslation->getTitle());
            $actualityTranslation->setContent($draftTranslation->getContent());
            $actualityTranslation->setSlug($draftTranslation->getSlug());
            $actualityTranslation->setImageLocale($draftTranslation->getImageLocale());
            $actualityTranslation->setImgLicence($draftTranslation->getImgLicence());
            $actualityTranslation->setImgLegend($draftTranslation->getImgLegend());

            $actuality->addTranslation($actualityTranslation);
        }

        foreach ($draft->getTaxonomies() as $draftTaxonomy) {
            $actuality->addTaxonomy($draftTaxonomy);
        }
        // Save last modifications.
        $this->em->flush();
    }

    /**
     * Create an Actuality from an ActualityDraft then associated it to the ActualityDraft object.
     *
     * @param ActualityDraft $draft
     */
    public function createActuality(ActualityDraft $draft)
    {
        $actuality = new Actuality();
        if (!empty($draft->getPublishedAt())) {
            $actuality->setPublishedAt($draft->getPublishedAt());
        } else {
            $actuality->setPublishedAt($this->now);
        }
        $actuality->setUpdatedBy($this->security->getUser());
        $actuality->setCreatedBy($this->security->getUser());
        $actuality->setCreatedAt($this->now);
        $actuality->setUpdatedAt($this->now);

        /** @var ActualityDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new ActualityTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setSlug($draftTranslation->getSlug());
            $translation->setImageLocale($draftTranslation->getImageLocale());
            $translation->setImgLicence($draftTranslation->getImgLicence());
            $translation->setImgLegend($draftTranslation->getImgLegend());

            $actuality->addTranslation($translation);
        }
        /** @var Taxonomy $taxonomy */
        foreach ($draft->getTaxonomies() as $taxonomy) {
            $actuality->addTaxonomy($taxonomy);
        }

        $draft->setActuality($actuality);
        $this->em->persist($actuality);
        $this->em->flush();
    }

    /**
     * Deleting the draft when it is published.
     *
     * @param ActualityDraft $draft
     */
    public function deleteDraft(ActualityDraft $draft)
    {
        $this->em->remove($draft);
        $this->em->flush();
    }

    /**
     * Find an ActualityDraft associated to an Actuality or create a new one from Actuality object.
     *
     * @param Actuality $actuality
     *
     * @return ActualityDraft
     */
    public function findOrCreateDraft(Actuality $actuality)
    {
        $actualityDraft = $this->em->getRepository(ActualityDraft::class)->findOneCompleteByActuality($actuality);

        if (!$actualityDraft instanceof ActualityDraft) {
            $actualityDraft = new ActualityDraft();

            $actualityDraft->setUpdatedBy($this->security->getUser());
            $actualityDraft->setCreatedBy($this->security->getUser());
            $actualityDraft->setCreatedAt($this->now);
            $actualityDraft->setUpdatedAt($this->now);
            $actualityDraft->setActuality($actuality);

            /** @var ActualityTranslation $actualityTranslation */
            foreach ($actuality->getTranslations() as $actualityTranslation) {
                $translation = new ActualityDraftTranslation();
                $translation->setLocale($actualityTranslation->getLocale());
                $translation->setTitle($actualityTranslation->getTitle());
                $translation->setContent($actualityTranslation->getContent());
                $translation->setSlug($actualityTranslation->getSlug());
                $translation->setImageLocale($actualityTranslation->getImageLocale());
                $translation->setImgLicence($actualityTranslation->getImgLicence());
                $translation->setImgLegend($actualityTranslation->getImgLegend());

                $actualityDraft->addTranslation($translation);
            }

            /** @var Taxonomy $taxonomy */
            foreach ($actuality->getTaxonomies() as $taxonomy) {
                $actualityDraft->addTaxonomy($taxonomy);
            }

            $this->em->persist($actualityDraft);
            $this->em->flush();
        }

        return $actualityDraft;
    }

    /**
     * Duplicate a draft news
     *
     * @param ActualityDraft $originalDraft
     * @return ActualityDraft
     */
    public function duplicate(ActualityDraft $originalDraft)
    {
        $clonedDraft = clone $originalDraft;

        $clonedDraft->setUpdatedBy($this->security->getUser());
        $clonedDraft->setCreatedBy($this->security->getUser());
        $clonedDraft->setCreatedAt($this->now);
        $clonedDraft->setUpdatedAt($this->now);
        $clonedDraft->setActuality(null);

        /** @var ActualityTranslation $originalDraftTranslation */
        foreach ($originalDraft->getTranslations() as $originalDraftTranslation) {
            $translation = new ActualityDraftTranslation();
            $translation->setLocale($originalDraftTranslation->getLocale());
            $translation->setTitle($originalDraftTranslation->getTitle() . '_Copy');
            $translation->setContent($originalDraftTranslation->getContent());
            $translation->setSlug($originalDraftTranslation->getSlug());
            $translation->setImageLocale($originalDraftTranslation->getImageLocale());
            $translation->setImgLicence($originalDraftTranslation->getImgLicence());
            $translation->setImgLegend($originalDraftTranslation->getImgLegend());

            $clonedDraft->addTranslation($translation);
        }

        /** @var Taxonomy $taxonomy */
        foreach ($originalDraft->getTaxonomies() as $taxonomy) {
            $clonedDraft->addTaxonomy($taxonomy);
        }

        // Save the cloned item to the database
        $this->em->persist($clonedDraft);
        $this->em->flush();

        return $clonedDraft;
    }
}

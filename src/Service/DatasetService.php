<?php                                      
                                                     
namespace App\Service;

use App\Entity\Dataset;
use App\Entity\DatasetDraft;
use App\Entity\DatasetDraftTranslation;
use App\Entity\DatasetTranslation;
use App\Entity\Taxonomy;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DatasetService
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

    /**
     * Publish a DatasetDraft ie create/update a Dataset associated to the DatasetDraft object.
     *
     * @param DatasetDraft $draft
     */
    public function publish(DatasetDraft $draft)
    {
        if ($draft->getDataset()) {
            $this->updateDataset($draft);
            $this->deleteDraft($draft);
            return;
        }
        $this->createDataset($draft);
        $this->deleteDraft($draft);
    }

    /**
     * Update Dataset properties with DatasetDraft properties.
     *
     * @param DatasetDraft $draft
     */
    public function updateDataset(DatasetDraft $draft)
    {
        $dataset = $draft->getDataset();
        $dataset->setUpdatedAt($this->now);
        $dataset->setUpdatedBy($this->security->getUser());
        $dataset->setActuality($draft->getActuality());
        $dataset->setDatasetQuote($draft->getDatasetQuote());
        $dataset->setPersistentId($draft->getPersistentId());
        $dataset->setLinkDataverse($draft->getLinkDataverse());

        // Remove Translations and Taxonomies collection.
        /** @var DatasetTranslation $translation */
        foreach ($dataset->getTranslations() as $translation) {
            $dataset->removeTranslation($translation);
            $this->em->remove($translation);
        }
        foreach ($dataset->getTaxonomies() as $taxonomy) {
            $dataset->removeTaxonomy($taxonomy);
            $this->em->remove($taxonomy);
        }
        // Execute actual translations and taxonomies removing from DB.
        $this->em->flush();
        // Added new collection of translations and taxonomies.
        /** @var DatasetDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new DatasetTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setHook($draftTranslation->getHook());
            $translation->setSlug($draftTranslation->getSlug());
            $translation->setImageLocale($draftTranslation->getImageLocale());
            $translation->setImgLicence($draftTranslation->getImgLicence());
            $translation->setImgLegend($draftTranslation->getImgLegend());

            $dataset->addTranslation($translation);
        }
        /** @var Taxonomy $draftTaxonomy */
        foreach ($draft->getTaxonomies() as $draftTaxonomy) {
            $dataset->addTaxonomy($draftTaxonomy);
        }

        // Save last modifications.
        $this->em->flush();
    }

    /**
     * Create a Dataset from a DatasetDraft then associated it to the DatasetDraft object.
     *
     * @param DatasetDraft $draft
     */
    public function createDataset(DatasetDraft $draft)
    {
        $dataset = new Dataset();

        $dataset->setUpdatedBy($this->security->getUser());
        $dataset->setCreatedBy($this->security->getUser());
        $dataset->setCreatedAt($this->now);
        $dataset->setUpdatedAt($this->now);

        $dataset->setActuality($draft->getActuality());
        $dataset->setDatasetQuote($draft->getDatasetQuote());
        $dataset->setPersistentId($draft->getPersistentId());
        $dataset->setLinkDataverse($draft->getLinkDataverse());

        /** @var DatasetDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new DatasetTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setTitle($draftTranslation->getTitle());
            $translation->setContent($draftTranslation->getContent());
            $translation->setHook($draftTranslation->getHook());
            $translation->setSlug($draftTranslation->getSlug());
            $translation->setImageLocale($draftTranslation->getImageLocale());
            $translation->setImgLicence($draftTranslation->getImgLicence());
            $translation->setImgLegend($draftTranslation->getImgLegend());

            $dataset->addTranslation($translation);
        }
        /** @var Taxonomy $taxonomy */
        foreach ($draft->getTaxonomies() as $taxonomy) {
            $dataset->addTaxonomy($taxonomy);
        }

        $draft->setDataset($dataset);
        $this->em->persist($dataset);
        $this->em->flush();
    }

    /**
     * Deleting the draft when it is published.
     *
     * @param DatasetDraft $draft
     */
    public function deleteDraft(DatasetDraft $draft)
    {
        $this->em->remove($draft);
        $this->em->flush();
    }

    /**
     * Find a DatasetDraft associated to a Dataset or create a new one from Dataset object.
     *
     * @param Dataset $dataset
     *
     * @return DatasetDraft
     */
    public function findOrCreateDraft(Dataset $dataset)
    {
        $datasetDraft = $this->em->getRepository(DatasetDraft::class)->findOneCompleteByDataset($dataset);

        if (!$datasetDraft instanceof DatasetDraft) {
            $datasetDraft = new DatasetDraft();

            $datasetDraft->setUpdatedBy($this->security->getUser());
            $datasetDraft->setCreatedBy($this->security->getUser());
            $datasetDraft->setCreatedAt($this->now);
            $datasetDraft->setUpdatedAt($this->now);
            $datasetDraft->setDataset($dataset);

            $datasetDraft->setActuality($dataset->getActuality());
            $datasetDraft->setDatasetQuote($dataset->getDatasetQuote());
            $datasetDraft->setPersistentId($dataset->getPersistentId());
            $datasetDraft->setLinkDataverse($dataset->getLinkDataverse());

            /** @var DatasetTranslation $datasetTranslation */
            foreach ($dataset->getTranslations() as $datasetTranslation) {
                $translation = new DatasetDraftTranslation();
                $translation->setLocale($datasetTranslation->getLocale());
                $translation->setTitle($datasetTranslation->getTitle());
                $translation->setContent($datasetTranslation->getContent());
                $translation->setHook($datasetTranslation->getHook());
                $translation->setSlug($datasetTranslation->getSlug());
                $translation->setImageLocale($datasetTranslation->getImageLocale());
                $translation->setImgLicence($datasetTranslation->getImgLicence());
                $translation->setImgLegend($datasetTranslation->getImgLegend());

                $datasetDraft->addTranslation($translation);
            }
            /** @var Taxonomy $taxonomy */
            foreach ($dataset->getTaxonomies() as $taxonomy) {
                $datasetDraft->addTaxonomy($taxonomy);
            }

            $this->em->persist($datasetDraft);
            $this->em->flush();
        }

        return $datasetDraft;
    }

    /**
     * Duplicate a draft dataset
     *
     * @param DatasetDraft $originalDraft
     * @return DatasetDraft
     */
    public function duplicate(DatasetDraft $originalDraft)
    {
        $clonedDraft = clone $originalDraft;

        $clonedDraft->setUpdatedBy($this->security->getUser());
        $clonedDraft->setCreatedBy($this->security->getUser());
        $clonedDraft->setCreatedAt($this->now);
        $clonedDraft->setUpdatedAt($this->now);
        $clonedDraft->setDataset(null);

        $clonedDraft->setActuality($originalDraft->getActuality());
        $clonedDraft->setDatasetQuote($originalDraft->getDatasetQuote());
        $clonedDraft->setPersistentId($originalDraft->getPersistentId());
        $clonedDraft->setLinkDataverse($originalDraft->getLinkDataverse());

        /** @var DatasetTranslation $originalDraftTranslation */
        foreach ($originalDraft->getTranslations() as $originalDraftTranslation) {
            $translation = new DatasetDraftTranslation();
            $translation->setLocale($originalDraftTranslation->getLocale());
            $translation->setTitle($originalDraftTranslation->getTitle() . '_Copy');
            $translation->setContent($originalDraftTranslation->getContent());
            $translation->setHook($originalDraftTranslation->getHook());
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

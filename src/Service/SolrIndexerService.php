<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Solarium\Client;
use App\Entity\Actuality;
use App\Entity\Dataset;
use App\Entity\Page;
use App\Entity\Event;
use App\Entity\Guide;
use App\Entity\Glossary;
use App\Entity\FaqBlock;
use App\Entity\DataWorkshop;
use App\Entity\Institution;
use App\Entity\ProjectTeam;
use App\Repository\ActualityRepository;
use App\Repository\DatasetRepository;
use App\Repository\PageRepository;
use App\Repository\EventRepository;
use App\Repository\GuideRepository;
use App\Repository\GlossaryRepository;
use App\Repository\FaqBlockRepository;
use App\Repository\DataWorkshopRepository;
use App\Repository\InstitutionRepository;
use App\Repository\ProjectTeamRepository;

/**
 * @codeCoverageIgnore
 * 
 * Class IndexerService
 *
 * @package App\Service
 */
class SolrIndexerService
{
    /**
     * @param SolrService
     */
    private $solrService;

    /**
     * @var ActualityRepository
     */
    private $actualityRepository;

    /**
     * @var DatasetRepository
     */
    private $datasetRepository;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var GuideRepository
     */
    private $guideRepository;

    /**
     * @var GlossaryRepository
     */
    private $glossaryRepository;

    /**
     * @var FaqBlockRepository
     */
    private $faqBlockRepository;

    /**
     * @var DataWorkshopRepository
     */
    private $dataWorkshopRepository;

    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    /**
     * @var ProjectTeamRepository
     */
    private $projectTeamRepository;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param ContainerInterface      $container
     * @param SolrService             $solrService
     * @param ActualityRepository     $actualityRepository
     * @param DatasetRepository       $datasetRepository
     * @param PageRepository          $pageRepository
     * @param EventRepository         $eventRepository
     * @param GuideRepository         $guideRepository
     * @param GlossaryRepository      $glossaryRepository
     * @param FaqBlockRepository      $faqBlockRepository
     * @param DataWorkshopRepository  $dataWorkshopRepository
     * @param InstitutionRepository   $institutionRepository
     * @param ProjectTeamRepository   $projectTeamRepository
     */
    public function __construct(
        ContainerInterface     $container,
        SolrService            $solrService,
        ActualityRepository    $actualityRepository,
        DatasetRepository      $datasetRepository,
        PageRepository         $pageRepository,
        EventRepository        $eventRepository,
        GuideRepository        $guideRepository,
        GlossaryRepository     $glossaryRepository,
        FaqBlockRepository     $faqBlockRepository,
        DataWorkshopRepository $dataWorkshopRepository,
        InstitutionRepository  $institutionRepository,
        ProjectTeamRepository  $projectTeamRepository
    ) {
        $this->solrService = $solrService;
        $this->locales = explode('|', $container->getParameter('app_locales'));
        $this->actualityRepository = $actualityRepository;
        $this->datasetRepository = $datasetRepository;
        $this->pageRepository = $pageRepository;
        $this->eventRepository = $eventRepository;
        $this->guideRepository = $guideRepository;
        $this->glossaryRepository = $glossaryRepository;
        $this->faqBlockRepository = $faqBlockRepository;
        $this->dataWorkshopRepository = $dataWorkshopRepository;
        $this->institutionRepository = $institutionRepository;
        $this->projectTeamRepository = $projectTeamRepository;
    }

    /**
     * Get existing solr client or create a new one.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->solrService->getClient();
    }

    /**
     * Add Document in Sorl
     *
     * @param object $entity
     * @param string $prefix
     * @param boolean $replace
     * @return void
     */
    public function addDocument($entity, $prefix, $replace = false)
    {
        // Ensures the solr doc is deleted.
        if ($replace) {
            $this->deleteById($entity->getId());
        }

        $update = $this->getClient()->createUpdate();
        $doc = $update->createDocument();

        $doc->id = $prefix . $entity->getId();

        foreach ($this->locales as $locale) {
            if (!$entity instanceof DataWorkshop && !$entity instanceof Institution && !$entity instanceof Glossary
            && !$entity instanceof ProjectTeam) {
                $doc->{'title_' . $locale} = $entity->translate($locale)->getTitle();
                $doc->{'content_' . $locale} = $entity->translate($locale)->getContent();

                if (!$entity instanceof FaqBlock) {
                    $doc->{'slug_' . $locale} = $entity->translate($locale)->getSlug();
                }
            }

            if (!$entity instanceof Page && !$entity instanceof DataWorkshop && !$entity instanceof Institution 
            && !$entity instanceof Glossary && !$entity instanceof FaqBlock && !$entity instanceof ProjectTeam
            && !$entity instanceof Event) {
                $doc->{'imgLicence_' . $locale} = $entity->translate($locale)->getImgLicence();
                $doc->{'imgLegend_' . $locale} = $entity->translate($locale)->getImgLegend();
            }

            if ($entity instanceof Dataset) {
                $doc->{'datasetQuote_' . $locale} = $entity->getDatasetQuote();
            }

            if ($entity instanceof DataWorkshop || $entity instanceof Institution || $entity instanceof ProjectTeam) {
                $doc->{'description_' . $locale} = $entity->translate($locale)->getDescription();
            }

            if ($entity instanceof Glossary) {
                $doc->{'term_' . $locale} = $entity->translate($locale)->getTerm();
                $doc->{'term_plural_' . $locale} = $entity->translate($locale)->getPlural();
                $doc->{'definition_' . $locale} = $entity->translate($locale)->getDefinition();
            }

            if ($entity instanceof ProjectTeam) {
                $doc->{'name_' . $locale} = $entity->getName();
                $doc->{'role_' . $locale} = $entity->translate($locale)->getRole();
            }
        }

        // Send the update query to Solr server
        $update->addDocument($doc);
        $update->addCommit();
        $this->getClient()->update($update);
        
        return true;
    }

    /**
     * Add News to Solr index.
     * @param Actuality $actuality
     * @return bool
     */
    public function addNews(Actuality $actuality): bool
    {
        return $this->addDocument($actuality, 'news_');
    }

    /**
     * Add Datasets to Solr index.
     * @param Dataset $dataset
     * @return bool
     */
    public function addDataset(Dataset $dataset): bool
    {
        return $this->addDocument($dataset, 'dataset_');
    }

    /**
     * Add Pages to Solr index.
     * @param Page $page
     * @return bool
     */
    public function addPage(Page $page): bool
    {
        return $this->addDocument($page, 'page_');
    }

    /**
     * Add Events to Solr index.
     * @param Event $event
     * @return bool
     */
    public function addEvent(Event $event): bool
    {
        return $this->addDocument($event, 'event_');
    }

    /**
     * Add Guides to Solr index.
     * @param Guide $guide
     * @return bool
     */
    public function addGuide(Guide $guide): bool
    {
        return $this->addDocument($guide, 'guide_');
    }

    /**
     * Add Terms to Solr index.
     * @param Glossary $term
     * @return bool
     */
    public function addGlossary(Glossary $term): bool
    {
        return $this->addDocument($term, 'term_');
    }

    /**
     * Add Faqs to Solr index.
     * @param FaqBlock $faq
     * @return bool
     */
    public function addFaq(FaqBlock $faq): bool
    {
        return $this->addDocument($faq, 'faq_');
    }

    /**
     * Add DataWorkshops to Solr index.
     * @param DataWorkshop $dataworkshop
     * @return bool
     */
    public function addDataWorkshop(DataWorkshop $dataworkshop): bool
    {
        return $this->addDocument($dataworkshop, 'dataworkshop_');
    }

    /**
     * Add Institutions to Solr index.
     * @param Institution $institution
     * @return bool
     */
    public function addInstitution(Institution $institution): bool
    {
        return $this->addDocument($institution, 'institution_');
    }

    /**
     * Add Team Members to Solr index.
     * @param ProjectTeam $member
     * @return bool
     */
    public function addProjectTeam(ProjectTeam $member): bool
    {
        return $this->addDocument($member, 'member_');
    }

    /**
     * Reindex all entities.
     *
     * @return int number of items indexed.
     */
    public function indexAll(): int
    {
        $indexedCount = 0;

        // Cleanup previous indexation.
        $this->deleteAll();

        foreach ($this->actualityRepository->findAll() as $actuality) {
            $indexedCount += $this->addNews($actuality) ? 1 : 0;
        }

        foreach ($this->datasetRepository->findAll() as $dataset) {
            $indexedCount += $this->addDataset($dataset) ? 1 : 0;
        }

        foreach ($this->pageRepository->findAll() as $page) {
            $indexedCount += $this->addPage($page) ? 1 : 0;
        }

        foreach ($this->eventRepository->findAll() as $event) {
            $indexedCount += $this->addEvent($event) ? 1 : 0;
        }

        foreach ($this->guideRepository->findAll() as $guide) {
            $indexedCount += $this->addGuide($guide) ? 1 : 0;
        }

        foreach ($this->glossaryRepository->findAll() as $term) {
            $indexedCount += $this->addGlossary($term) ? 1 : 0;
        }

        foreach ($this->faqBlockRepository->findAll() as $faq) {
            $indexedCount += $this->addFaq($faq) ? 1 : 0;
        }
        
        foreach ($this->dataWorkshopRepository->findAll() as $dataworkshop) {
            $indexedCount += $this->addDataWorkshop($dataworkshop) ? 1 : 0;
        }

        foreach ($this->institutionRepository->findAll() as $institution) {
            $indexedCount += $this->addInstitution($institution) ? 1 : 0;
        }

        foreach ($this->projectTeamRepository->findAll() as $member) {
            $indexedCount += $this->addProjectTeam($member) ? 1 : 0;
        }

        return $indexedCount;
    }

    /**
     * Deletes all data from current solr index.
     *
     * @return void
     */
    public function deleteAll(): void
    {
        $update = $this->getClient()->createUpdate();
        $update->addDeleteQuery('*');
        $update->addCommit();
        $this->getClient()->update($update);
    }

    /**
     * Delete document with given ID from Solr.
     * @param int $id
     */
    public function deleteById(int $id): void
    {
        $update = $this->getClient()->createUpdate();
        $update->addDeleteById($id);
        $update->addCommit();
        $this->getClient()->update($update);
    }

    /**
     * Delete all objects
     * @return void
     */
    public function deleteObjects(): void
    {
        // Add here class objects variables
        $objectList = [$news, $dataset, $page, $event, $guide, $term, $faq, $dataworkshop, $institution, $member];

        foreach ($objectList as $object) {
            $this->deleteById($object->getId());
        }
    }
}

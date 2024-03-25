<?php                                      
                                                     
namespace App\Service;

use App\Entity\Actuality;
use App\Entity\Dataset;
use App\Entity\Event;
use App\Entity\Institution;
use App\Entity\Lame\CarouselLame;
use App\Entity\Lame\CenterMapLame;
use App\Entity\Lame\HighlightedLame;
use App\Entity\Lame\Lame;
use App\Entity\Lame\NewsLame;
use App\Entity\Lame\SpotLightLame;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Returns laminas ready to use for frontend.
 */
class LaminaService
{
    public const TYPES = [

        'carousel'    => CarouselLame::class,
        'map'         => CenterMapLame::class,
        'highlighted' => HighlightedLame::class,
        'spotlight'   => SpotLightLame::class,
        'news'        => NewsLame::class,
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CenterMapCoordService
     */
    private $mapCoordService;


    /**
     * @param EntityManagerInterface $em
     * @param CenterMapCoordService  $mapCoordService
     */
    public function __construct(EntityManagerInterface $em, CenterMapCoordService $mapCoordService)
    {
        $this->em = $em;
        $this->mapCoordService = $mapCoordService;
    }

    /**
     * @param string $locale
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllLaminasOrdered($locale, $eventsLimit)
    {
        $laminas = [];
        $rawLaminas = $this->getLaminasIdOrdered($locale);

        foreach ($rawLaminas as $rawLamina) {
            $lamina = $this->em->getRepository(self::TYPES[$rawLamina['type']])->getFullInfoByIdAndLocale(
                $rawLamina['id'],
                $locale
            );
            // We remove NULL in case of specific condition removed the lamina.
            // (ex: the attached entity not available in locale).
            if ($lamina instanceof Lame) {
                $data = $this->getLaminaData($lamina, $locale, $eventsLimit);
                if (is_array($data)) {
                    $laminas[] = [
                        'type'   => $rawLamina['type'],
                        'id'     => $rawLamina['id'],
                        'lamina' => $data,
                    ];
                }
            }
        }
        return $laminas;
    }

    /**
     * @param $lamina
     * @param $locale
     *
     * @return array|false
     */
    protected function getLaminaData($lamina, $locale, $eventsLimit)
    {
        $class = get_class($lamina);
        $data = [];
        $noData = true;

        switch ($class) {
            case CarouselLame::class:
                if (false === $this->getCarouselLaminaData($locale, $data, $lamina)) {
                    $noData = false;
                }
                break;
            case CenterMapLame::class:
                $data = $this->getCenterMapLaminaData($lamina);
                break;
            case HighlightedLame::class:
                if (false === $this->getHighlightedLaminaData($locale, $data, $lamina)) {
                    $noData = false;
                }
                break;
            case NewsLame::class:
                if (false === $this->getNewsLaminaData($locale, $data, $lamina, $eventsLimit)) {
                    $noData = false;
                }
                break;
            case SpotLightLame::class:
                if (false === $this->getSpotLightLaminaData($locale, $data, $lamina)) {
                    $noData = false;
                }
                break;
            default:
                $noData = false;
                break;
        }
        if (false === $noData) {
            return false;
        }
        $data['title'] = $lamina->getTitle();
        return $data;
    }

    /**
     * @param string $locale
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLaminasIdOrdered($locale)
    {
        $conn = $this->em->getConnection();
        $sql = '';
        $maxType = count(self::TYPES);
        $i = 1;

        foreach (self::TYPES as $type => $className) {
            $sql .= $this->em->getRepository($className)->getRawSqlToUnion($type);
            if ($i < $maxType) {
                $sql .= ' UNION ';
            }
            $i++;
        }
        $sql .= ' ORDER BY weight';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['locale' => $locale, 'isPublished' => true]);
        return $result->fetchAllAssociative();
    }

    /**
     * @param string   $locale
     * @param array    $data
     * @param NewsLame $lamina
     *
     * @return bool
     */
    protected function getNewsLaminaData($locale, &$data, $lamina, $eventsLimit)
    {
        $news = [];
        $actualities = $this->loadActualities($locale, $lamina);
        $events = $this->loadEvents($locale, $lamina, $eventsLimit);

        if (count($actualities)) {
            $news['actualities'] = $actualities;
        }
        if (count($events)) {
            $news['events'] = $events;
        }
        if (!count($news)) {
            return false;
        }
        $data['news'] = $news;

        return true;
    }

    /**
     * @param string        $locale
     * @param array         $data
     * @param SpotLightLame $lamina
     *
     * @return bool
     */
    protected function getSpotlightLaminaData($locale, &$data, $lamina)
    {
        $spotlight = [];
        $datasets = $this->loadDatasets($locale, $lamina);

        // Get the Highlighted dataset ID if it exists
        $highlightedDatasetId = null;
        $highlightedLamina = $this->em->getRepository(HighlightedLame::class)->findOneBy([]);
        
        if ($highlightedLamina) {
            $highlightedDataset = $highlightedLamina->getDataset();
        
        if ($highlightedDataset instanceof Dataset) {
                $highlightedDatasetId = $highlightedDataset->getId();
            }
        }

        // Filter out the Highlighted dataset from the list of datasets
        $filteredDatasets = [];
        foreach ($datasets as $dataset) {
            if ($highlightedDatasetId !== $dataset->getId()) {
                $filteredDatasets[] = $dataset;
            }
        }

        // Ensure there are always 6 datasets displayed in Spotlight Lamina
        if (count($filteredDatasets) < 6) {
            $additionalDatasetsCount = 6 - count($filteredDatasets);
            $additionalDatasetIds = $this->em->getRepository(Dataset::class)->findNPublishedInLocaleExcludingId($locale, $additionalDatasetsCount, $highlightedDatasetId);
    
            foreach ($additionalDatasetIds as $rawDataset) {
                if (is_array($rawDataset) && isset($rawDataset['id'])) {
                    $datasetId = $rawDataset['id'];
                    $additionalDataset = $this->em->getRepository(Dataset::class)->findOneByIdWithLocale($datasetId, $locale);
                
                    if ($additionalDataset instanceof Dataset) {
                        $filteredDatasets[] = $additionalDataset;
                    }
                }
            }
        }

        if (count($filteredDatasets)) {
            $spotlight['datasets'] = $filteredDatasets;
        }

        if (!count($spotlight)) {
            return false;
        }

        $data['spotlight'] = $spotlight;

        return true;
    }

    /**
     * @param string   $locale
     * @param NewsLame $lamina
     *
     * @return array
     */
    protected function loadActualities($locale, $lamina)
    {
        $actualities = [];

        if (!$lamina->getAutoActu()) {
            if (!is_null($actu = $this->loadActuality($lamina->getActuFirst(), $locale))) {
                $actualities[] = $actu;
            }
            if (!is_null($actu = $this->loadActuality($lamina->getActuSecond(), $locale))) {
                $actualities[] = $actu;
            }
            if (!is_null($actu = $this->loadActuality($lamina->getActuThird(), $locale))) {
                $actualities[] = $actu;
            }
            if (!is_null($actu = $this->loadActuality($lamina->getActuFourth(), $locale))) {
                $actualities[] = $actu;
            }
        } else {
            $repo = $this->em->getRepository(Actuality::class);
            $rawActualities = $repo->findNPublishedInLocale($locale);
            $actualityIds = [];

            foreach ($rawActualities as $rawActuality) {
                $actualityIds[] = $rawActuality->getId();
            }
            if (count($actualityIds)) {
                $actualities = $repo->findAllByLocaleAndIds($locale, $actualityIds);
            }
        }

        return $actualities;
    }

    /**
     * @param string   $locale
     * @param NewsLame $lamina
     *
     * @return array
     */
    protected function loadEvents($locale, $lamina, $eventsLimit)
    {
        $events = [];

        if (!$lamina->getAutoEvent()) {
            if (!is_null($event = $this->loadEvent($lamina->getEventFirst(), $locale))) {
                $events[] = $event;
            }
            if (!is_null($event = $this->loadEvent($lamina->getEventSecond(), $locale))) {
                $events[] = $event;
            }
            if (!is_null($event = $this->loadEvent($lamina->getEventThird(), $locale))) {
                $events[] = $event;
            }
            if (!is_null($event = $this->loadEvent($lamina->getEventFourth(), $locale))) {
                $events[] = $event;
            }
            if (!is_null($event = $this->loadEvent($lamina->getEventFifth(), $locale))) {
                $events[] = $event;
            }
        } else {
            $repo = $this->em->getRepository(Event::class);
            $rawEvents = $repo->findNextPublishedLimited($locale, $eventsLimit);
            $eventIds = [];

            foreach ($rawEvents as $rawEvent) {
                $eventIds[] = $rawEvent->getId();
            }
            if (count($eventIds)) {
                $events = $repo->findAllByLocaleAndIdsOrdered($locale, $eventIds);
            }
        }
        return $events;
    }

    /**
     * @param string   $locale
     * @param NewsLame $lamina
     *
     * @return array
     */
    protected function loadDatasets($locale, $lamina)
    {
        $datasets = [];

        if (!$lamina->getAutoDataset()) {
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetFirst(), $locale))) {
                $datasets[] = $dataset;
            }
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetSecond(), $locale))) {
                $datasets[] = $dataset;
            }
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetThird(), $locale))) {
                $datasets[] = $dataset;
            }
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetFourth(), $locale))) {
                $datasets[] = $dataset;
            }
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetFifth(), $locale))) {
                $datasets[] = $dataset;
            }
            if (!is_null($dataset = $this->loadDataset($lamina->getDatasetSixth(), $locale))) {
                $datasets[] = $dataset;
            }
        } else {
            $repo = $this->em->getRepository(Dataset::class);
            $rawDatasets = $repo->findNPublishedInLocale($locale);
            $datasetIds = [];

            foreach ($rawDatasets as $rawDataset) {
                $datasetIds[] = $rawDataset->getId();
            }
            if (count($datasetIds)) {
                $datasets = $repo->findAllByLocaleAndIds($locale, $datasetIds);
            }
        }

        return $datasets;
    }

    /**
     * @param Actuality $actu
     * @param string    $locale
     *
     * @return null|Actuality
     */
    protected function loadActuality($actu, $locale)
    {
        if ($actu instanceof Actuality) {
            return $this->em->getRepository(Actuality::class)->findOneByIdWithLocale($actu->getId(), $locale);
        }
        return null;
    }

    /**
     * @param Event  $eventWithoutData
     * @param string $locale
     *
     * @return null|Event
     */
    protected function loadEvent($eventWithoutData, $locale)
    {
        if ($eventWithoutData instanceof Event) {
            return $this->em->getRepository(Event::class)->findOneByIdWithLocale($eventWithoutData->getId(), $locale);
        }
        return null;
    }

    /**
     * @param Dataset  $dataset
     * @param string   $locale
     *
     * @return null|Dataset
     */
    protected function loadDataset($dataset, $locale)
    {
        if ($dataset instanceof Dataset) {
            return $this->em->getRepository(Dataset::class)->findOneByIdWithLocale($dataset->getId(), $locale);
        }
        return null;
    }

    /**
     * @param string       $locale
     * @param array        $data
     * @param CarouselLame $lamina
     *
     * @return bool
     */
    public function getCarouselLaminaData($locale, &$data, $lamina)
    {
        $institutions = [];

        if ((!$lamina instanceof CarouselLame) || count($lamina->getEntities()) == 0) {
            return false;
        }
        $entities = $lamina->getEntities();
        foreach ($entities as $entity) {
            $institution = $this->em->getRepository(Institution::class)->findOneByIdWithLocale(
                $entity->getId(),
                $locale
            );
            if ($institution instanceof Institution) {
                $institutions[] = $institution;
            }
        }
        if (!count($institutions)) {
            return false;
        }
        $data['entities'] = $institutions;
        return true;
    }

    /**
     * @param CenterMapLame $lamina
     *
     * @return array
     */
    public function getCenterMapLaminaData($lamina)
    {
        return [
            'content' => $lamina->getContent(),
            'points' => $this->mapCoordService->getPoints($lamina),
        ];
    }

    /**
     * @param string          $locale
     * @param array           $data
     * @param HighlightedLame $lamina
     *
     * @return bool
     */
    public function getHighlightedLaminaData($locale, &$data, $lamina)
    {
        $dataset = $lamina->getDataset();
        if (!$dataset instanceof Dataset) {
            return false;
        }
        $entity = $this->em->getRepository(Dataset::class)->findOneByIdWithLocale($dataset->getId(), $locale);
        if (is_null($entity)) {
            return false;
        }
        $data['entity'] = $entity;

        return true;
    }
}

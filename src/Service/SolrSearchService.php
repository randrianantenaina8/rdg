<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Solarium\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Result\Result;

/**
 * @codeCoverageIgnore
 * Class SearchService
 *
 * @package App\Service
 */
class SolrSearchService 
{
    const DEFAULT_RESULTS_PER_PAGE = 10;
    const DEFAULT_CURRENT_PAGE = 1;
    const QUERY_FIELDS = [
        'title',
        'content',
        'description',
        'slug',
        'term',
        'term_plural',
        'definition',
        'imgLegend',
        'imgLicence',
        'hook',
        'datasetQuote',
        'name',
        'role'
    ];

    /**
     * @var SolrService
     */
    private $solrService;

    /**
     * @var string[]
     */
    private $locales;

    /**
     * SearchService constructor.
     *
     * @param TranslatorInterface $translator
     * @param ContainerInterface $container
     * @param SolrService $solrService
     */
    public function __construct(
        TranslatorInterface $translator,
        ContainerInterface  $container,
        SolrService         $solrService
    ) 
    {
        $this->solrService = $solrService;
        $this->locales = explode('|', $container->getParameter('app_locales'));
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
     * Get results from solr.
     *
     * @param string|null $searchString
     * @param int $currentPage
     * @param int $resultsPerPage
     * @param string|null $locale
     *
     * @return array
     */
    public function getResults(
        string $searchString = null,
        int    $currentPage = null,
        int    $resultsPerPage = null,
        string $locale = null
    ): array 
    {
        // Init locale and pagination.
        $locale = $locale ?? reset($this->locales);
        $currentPage = $currentPage ?? self::DEFAULT_CURRENT_PAGE;
        $resultsPerPage =  $resultsPerPage ?? self::DEFAULT_RESULTS_PER_PAGE;
        $result = $this->select($searchString, $currentPage, $resultsPerPage, $locale);

        return $result->getDocuments();
    }

    /**
     * Execute solr select query.
     *
     * @param string $searchString
     * @param int    $currentPage
     * @param int    $resultsPerPage
     * @param string $locale
     *
     * @return  ResultInterface|Result
     */
    public function select(
        $searchString,
        int $currentPage,
        int $resultsPerPage,
        string $locale
    ): ResultInterface 
    {
        // Init select and pagination.
        $query = $this->getClient()->createSelect();
        $query->setStart(($currentPage - 1) * $resultsPerPage);
        $query->setRows($resultsPerPage);

        // Creates query string.
        foreach (self::QUERY_FIELDS as $fieldName) {
            $queryParts[] = $fieldName . '_' . $locale . ':("' . $searchString . '")';
        }

        // Set the solr query.
        $queryString = implode(' OR ', $queryParts);
        $query->setQuery($queryString);

        /**
         * Sets fields returned from solr. 
         * Modify this if solr should only store IDs and not actual data.
         */
        $query->setFields(
            'id,
            title_fr,
            content_fr,
            description_fr,
            slug_fr,
            term_fr,
            term_plural_fr,
            name_fr,
            role_fr,
            definition_fr,
            imgLegend_fr,
            imgLicence_fr,
            datasetQuote_fr,
            title_en,
            content_en,
            description_en,
            slug_en,
            term_en,
            term_plural_en,
            role_en,
            definition_en,
            datasetQuote_en,
            imgLegend_en,
            imgLicence_en'
        );

        return $this->getClient()->select($query);
    }
}

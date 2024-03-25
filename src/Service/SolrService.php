<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\Http;

/**
 * @codeCoverageIgnore
 * Class SolrService
 *
 * @package App\Service
 */
class SolrService 
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Client
     */
    private $client;

    /**
     * SolrService constructor.
     *
     * @param ContainerInterface $container
     * @param array $config
     */
    public function __construct(
        ContainerInterface $container,
        array              $config
    ) {
        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->configuration = $config;
    }

    /**
     * Get existing solr client or create a new one.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $conf = $this->configuration;
        $this->client = $this->createClient($conf);
        $this->client->createEndpoint($conf + ['key' => 'rdg_solr'], true);
        $this->client->getAdapter()->setTimeout($this->configuration['timeout'] ?? 5);

        return $this->client;
    }

    /**
     * Creates a new Solr Client.
     * @param array $configuration
     * @return Client
     */
    protected function createClient($configuration = []): Client
    {
        if (Client::checkMinimal('5.2.0')) {
            return new Client(
                extension_loaded('curl') ? new Curl($configuration) : new Http($configuration),
                $this->eventDispatcher,
                $configuration
            );
        }

        return new Client(null, $this->eventDispatcher);
    }
}

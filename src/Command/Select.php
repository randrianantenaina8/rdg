<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\SolrSearchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Select
 *
 * @package App\Command
 */
class Select extends Command
{
    /**
     * @var SolrSearchService
     */
    protected $searchService;

    /**
     * Select constructor.
     *
     * @param SolrSearchService $searchService
     * @param string|null $name
     */
    public function __construct(
        SolrSearchService $searchService,
        string            $name = null
    ) {
        $this->searchService = $searchService;
        parent::__construct($name);
    }

    /**
     * Configuration method.
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('rdg:select');
        $this->setDescription('Run a search query in solr.');
        $this->setHelp('Select resources in solr based on given search input which will be used as query string. Only print first 10 results.');
        $this->addArgument('search', InputArgument::REQUIRED, 'The full text search string');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            print_r($this->searchService->getResults($input->getArgument('search')), true)
        );

        return Command::SUCCESS;
    }
}

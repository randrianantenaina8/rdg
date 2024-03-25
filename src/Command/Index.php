<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\SolrIndexerService;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @codeCoverageIgnore
 * Class Index
 *
 * @package App\Command
 */
class Index extends Command
{
    /**
     * @var SolrIndexerService
     */
    protected $indexerService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Index constructor.
     *
     * @param SolrIndexerService  $indexerService
     * @param TranslatorInterface $translator
     * @param string|null         $name
     */
    public function __construct(
        SolrIndexerService  $indexerService,
        TranslatorInterface $translator,
        string              $name = null
    ) {
        $this->indexerService = $indexerService;
        $this->translator = $translator;
        parent::__construct($name);
    }

    /**
     * Configuration method.
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('rdg:index');
        $this->setDescription('Index datasets and activities in solr.');
        $this->setHelp('Send all data relative to datasets/activities and their metas in solr search engine for indexation.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->indexAll($input, $output);
        $output->writeln($this->translator->trans('bo.config.solr.indexation.finished'));
        
        return Command::SUCCESS;
    }

    /**
     * Index all resources.
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function indexAll(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('');
        $indexedContentCount = $this->indexerService->indexAll();
        $output->writeln($indexedContentCount . $this->translator->trans('bo.config.solr.indexed.content'));
    }
}

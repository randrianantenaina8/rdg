<?php                                      
                                                     
namespace App\Command;

use App\Service\MigrationUrlService;
use App\Entity\Actuality;
use App\Entity\ActualityTranslation;
use App\Entity\ActualityDraftTranslation;
use App\Entity\Dataset;
use App\Entity\DatasetTranslation;
use App\Entity\DatasetDraftTranslation;
use App\Entity\DataWorkshopTranslation;
use App\Entity\EventTranslation;
use App\Entity\FaqBlockTranslation;
use App\Entity\Guide;
use App\Entity\GuideTranslation;
use App\Entity\GuideDraftTranslation;
use App\Entity\Institution;
use App\Entity\InstitutionTranslation;
use App\Entity\MenuBasic;
use App\Entity\MenuMultiple;
use App\Entity\PageTranslation;
use App\Entity\PageDraftTranslation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UrlMigrationCommand extends Command
{
    public const CMDNAME = 'app:url-migration';

    public const MIGRATE_URLS = [
        'Actualities' => ActualityTranslation::class,
        'ActualitiesDraft' => ActualityDraftTranslation::class,
        'Dataset' => DatasetTranslation::class,
        'DatasetDraft' => DatasetDraftTranslation::class,
        'Events' => EventTranslation::class,
        'FaqBlock' => FaqBlockTranslation::class,
        'Guide' => GuideTranslation::class,
        'GuideDraft' => GuideDraftTranslation::class,
        'Pages' => PageTranslation::class,
        'PageDraft' => PageDraftTranslation::class,
    ];

    public const MIGRATE_DESCRIPTION_URLS = [
        'DataWorkshop' => DataWorkshopTranslation::class,
        'Institution' => InstitutionTranslation::class,
    ];

    public const MIGRATE_EXTERNAL_LINKS = [
        'MenuBasic' => MenuBasic::class,
        'MenuMultiple' => MenuMultiple::class,
    ];

    public const MIGRATE_IMAGE_URLS = [
        'Actualities' => Actuality::class,
        'Datasets' => Dataset::class,
        'Guides' => Guide::class,
        'Institutions' => Institution::class,
    ];

    /**
     * @var MigrationUrlService
     */
    private $migrationUrlService;


    /**
     * @param MigrationUrlService $migrationUrlService
     */
    public function __construct(MigrationUrlService $migrationUrlService)
    {
        $this->migrationUrlService = $migrationUrlService;

        parent::__construct();
    }

    /**
     * Command configuration.
     */
    public function configure()
    {
        $this
            ->setName(self::CMDNAME)
            ->setDescription('Migrates all wysiwyg test url to production url.')
            ->setHelp('This command migrates all wysiwyg test url to production url.')
        ;
    }

    protected function getError($output, $e)
    {
        $output->writeln([
            'Migration Error',
            '============',
            $e->getMessage(),
        ]);

        return Command::FAILURE;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Url Migration',
            '============',
            "Migrating urls..." . PHP_EOL,
        ]);
        
        foreach (self::MIGRATE_URLS as $key => $migrationUrl) {
            try {
                $nbMigrations = $this->migrationUrlService->migrateUrls($migrationUrl, $key);
                $output->writeln($key . " - Number of migrations: " . $nbMigrations . PHP_EOL);
            } 
            catch (\Exception $e) {
                return $this->getError($output, $e);
            }       
        }

        foreach (self::MIGRATE_DESCRIPTION_URLS as $key => $migrationDescriptionUrl) {
            try {
                $nbMigrations = $this->migrationUrlService->migrateDescriptionUrls($migrationDescriptionUrl, $key);
                $output->writeln($key . " - Number of migrations: " . $nbMigrations . PHP_EOL);
            } 
            catch (\Exception $e) {
                return $this->getError($output, $e);
            }       
        }

        foreach (self::MIGRATE_EXTERNAL_LINKS as $key => $migrationExternalLink) {
            try {
                $nbMigrations = $this->migrationUrlService->migrateMenuUrls($migrationExternalLink, $key);
                $output->writeln($key . " - Number of migrations: " . $nbMigrations . PHP_EOL);
            } 
            catch (\Exception $e) {
                return $this->getError($output, $e);
            }       
        }

        foreach (self::MIGRATE_IMAGE_URLS as $key => $migrationImageUrl) {
            try {
                $nbMigrations = $this->migrationUrlService->migrateImageUrls($migrationImageUrl, $key);
                $output->writeln($key . " - Number of migrations: " . $nbMigrations . PHP_EOL);
            } 
            catch (\Exception $e) {
                return $this->getError($output, $e);
            }       
        }

        try {
            $nbMigrations = $this->migrationUrlService->migrateGlossaryUrls();
            $output->writeln("Glossary - Number of migrations: " . $nbMigrations . PHP_EOL);
        } 
        catch (\Exception $e) {
            return $this->getError($output, $e);
        }     
        
        $output->writeln([
            'Whoa!',
            'You are about to:',
            'Migrate all the url to production domain.'
        ]);

        return Command::SUCCESS;
    }
}

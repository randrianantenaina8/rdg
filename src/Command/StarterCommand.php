<?php                                      
                                                     
namespace App\Command;

use App\Entity\Config;
use App\Entity\MenuBasic;
use App\Entity\MenuBasicTranslation;
use App\Entity\MenuMultiple;
use App\Entity\MenuMultipleTranslation;
use App\Entity\Page;
use App\Entity\PageTranslation;
use App\Entity\Subject;
use App\Entity\SubjectTranslation;
use App\Entity\User;
use App\Tool\DateTool;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StarterCommand extends Command
{
    /**
     * Page entities to start the website that will be used in different menus.
     * First key is going to be used to link with footer and header menus.
     */
    public const START_PAGES = [
        'Accessibilité' => [
            'fr' => [
                'title' => 'Accessibilité',
                'content' => 'Accessibilité',
                'slug' => 'accessibilite',
            ],
            'en' => [
                'title' => 'Accessibility',
                'content' => 'Accessibility',
                'slug' => 'accessibility',
            ]
        ],
        'Mentions légales' => [
            'fr' => [
                'title' => 'Mentions légales',
                'content' => 'Mentions légales',
                'slug' => 'mentions-legales'
            ],
            'en' => [
                'title' => 'Legal notice',
                'content' => 'Legal notice',
                'slug' => 'legal-notice',
            ]
        ],
        'Données personnelles' => [
            'fr' => [
                'title' => 'Données personnelles',
                'content' => 'Données personnelles',
                'slug' => 'donnees-personnelles'
            ],
            'en' => [
                'title' => 'Personal data',
                'content' => 'Personal data',
                'slug' => 'personal-data',
            ]
        ],
        'A propos' => [
            'fr' => [
                'title' => 'A propos',
                'content' => 'A propos',
                'slug' => 'a-propos'
            ],
            'en' => [
                'title' => 'About us',
                'content' => 'About us',
                'slug' => 'about-us',
            ]
        ],
        'Entrepôts thématiques' => [
            'fr' => [
                'title' => 'Entrepôts thématiques',
                'content' => 'Entrepôts thématiques',
                'slug' => 'entrepots-thematiques'
            ],
            'en' => [
                'title' => 'Thematic dataRepositories',
                'content' => 'Thematic dataRepositories',
                'slug' => 'thematic-dataRepositories',
            ]
        ],
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * User used in association with entities that will be created.
     *
     * @var User|null
     */
    private $userOne;

    /**
     * @param EntityManagerInterface $em
     * @param string|null            $name
     */
    public function __construct(EntityManagerInterface $em, string $name = null)
    {
        $this->em = $em;
        $this->userOne = null;

        parent::__construct($name);
    }

    /**
     * Command configuration.
     */
    protected function configure()
    {
        $this
            // The name of the command (the part after "bin/console")
            ->setName('app:starter-site')
            // The short description shown while running "php bin/console list"
            ->setDescription("Setting up initial data when database is empty.")
            // The full command description shown when running the command with the "--help" option
            ->setHelp("This command is going to set up initial settings and create mandatory page entities.");
    }

    /**
     * Main function.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Page entities creation starts...',
        ]);
        try {
            $this->createPageEntities();
        } catch (\Exception $pageExcept) {
            $output->writeln([
                '',
                'FAILED to create page entities...',
                $pageExcept->getMessage(),
                ''
            ]);
            return Command::FAILURE;
        }
        $output->writeln([
            'Page entities created!',
            '',
            '======================',
            '',
        ]);

        $output->writeln([
            'Adding system routes in configuration starts...',
        ]);
        try {
            $this->createConfigRoutes();
        } catch (\Exception $routeExcept) {
            $output->writeln([
                '',
                'FAILED to add system routes in config...',
                $routeExcept->getMessage(),
                ''
            ]);
            return Command::FAILURE;
        }
        $output->writeln([
            'System routes successfully added in config!',
            '',
            '======================',
            '',
        ]);

        $output->writeln([
            'Default contact subject creation starts...',
        ]);
        try {
            $contactSubjectCreated = $this->createContactSubject();
        } catch (\Exception $subjectExcept) {
            $output->writeln([
                '',
                'FAILED to create contact subject',
                $subjectExcept->getMessage(),
                ''
            ]);
            return Command::FAILURE;
        }
        $contactSubjectMsg = 'Default contact subject successfully created!';
        if (false === $contactSubjectCreated) {
            $contactSubjectMsg = "Contact subject already exists in database.";
        }
        $output->writeln([
            $contactSubjectMsg,
            '',
            '======================',
            '',
        ]);

        return Command::SUCCESS;
    }

    /**
     * Create pages entities mandatory.
     */
    protected function createPageEntities()
    {
        $createdDate = DateTool::datetimeNow();

        foreach (self::START_PAGES as $key => $startPage) {
            $page = $this->getPage($startPage, $createdDate);
            if ($page instanceof Page) {
                $this->em->persist($page);
                echo $page->getId();
            }
        }
        $this->em->flush();
    }

    /**
     * Create System routes configuration in database.
     */
    protected function createConfigRoutes()
    {
        $create = false;

        $systemRoutesConfig = $this->em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
        if (!$systemRoutesConfig) {
            $systemRoutesConfig = new Config();
            $systemRoutesConfig->setName(Config::ROUTE);
            $create = true;
        }
        // default value for data is empty array... So whatever the entity exists or not.
        $systemRoutes = $systemRoutesConfig->getData();
        foreach (Config::ROUTES as $label => $route) {
            $systemRoutes[$label] = $route;
        }
        $systemRoutesConfig->setData($systemRoutes);
        if ($create) {
            $this->em->persist($systemRoutesConfig);
        }
        $this->em->flush();
    }

    /**
     * @param array     $data
     * @param \DateTime $date
     *
     * @return Page|null
     */
    protected function getPage($data, $date)
    {
        $page = new Page();

        foreach ($data as $locale => $dataTranslated) {
            if (!$this->isSlugAvailable(PageTranslation::class, $dataTranslated['slug'])) {
                return null;
            }
            $translation = new PageTranslation();
            $translation->setTitle($dataTranslated['title']);
            $translation->setContent($dataTranslated['content']);
            $translation->setSlug($dataTranslated['slug']);
            $translation->setLocale($locale);
            $page->addTranslation($translation);
            $page->setCreatedAt($date);
            $page->setUpdatedAt($date);
            $page->setCreatedBy($this->userOne);
            $page->setUpdatedBy($this->userOne);
        }

        return $page;
    }

    /**
     * @param string $className
     * @param string $slug
     *
     * @return bool
     */
    private function isSlugAvailable($className, $slug)
    {
        $repo = $this->em->getRepository($className);
        $resultFound = $repo->findBy(['slug' => $slug]);

        if (!is_array($resultFound) || count($resultFound)) {
            return false;
        }

        return true;
    }

    /**
     * Create a default contact subject.
     * None, if already one contact subject exists in database.
     *
     * @return bool
     */
    private function createContactSubject()
    {
        if (!count($this->em->getRepository(Subject::class)->findAll())) {
            $date = DateTool::datetimeNow();
            $subject = new Subject();
            $subject->setWeight(100);
            $subject->setSubject('Autre demande');
            $subject->setCreatedAt($date);
            $subject->setUpdatedAt($date);
            $subject->setCreatedBy($this->userOne);
            $subject->setUpdatedBy($this->userOne);

            $translationFrench = new SubjectTranslation();
            $translationFrench->setLocale('fr');
            $translationFrench->setSubject('Autre demande');
            $translationEnglish = new SubjectTranslation();
            $translationEnglish->setLocale('en');
            $translationEnglish->setSubject('Other request');
            $subject->addTranslation($translationFrench);
            $subject->addTranslation($translationEnglish);

            $this->em->persist($subject);
            $this->em->flush();
            return true;
        }

        return false;
    }
}

<?php                                      
                                                     
namespace App\Command;

use App\Entity\Actuality;
use App\Entity\ActualityTranslation;
use App\Entity\ProjectTeam;
use App\Entity\ProjectTeamTranslation;
use App\Entity\User;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sandbox command to use when database is empty (only) (or just data created by app:starter-site).
 */
class FakeDataCommand extends Command
{
    public const NB_ELEM = 28;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     * @param string|null            $name
     */
    public function __construct(EntityManagerInterface $em, string $name = null)
    {
        $this->em = $em;
        $this->user = null;

        $users = $this->em->getRepository(User::class)->findAll();
        if (is_iterable($users) && $users[0] instanceof User) {
            $this->user = $users[0];
        }

        parent::__construct($name);
    }

    /**
     * Command configuration.
     */
    public function configure()
    {
        $this
            ->setName('test:fake-data')
            ->setHelp('Sandbox command to create fake datas.')
        ;
    }

    /**
     * Sandbox use !
     * Do not re-use if you did not delete previous created by this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = DateTool::datetimeNow();

        // Create Actualities.
        for ($i = 0; $i < self::NB_ELEM; $i++) {
            $actu = new Actuality();
            $actu->setCreatedBy($this->user);
            $actu->setUpdatedBy($this->user);
            $actu->setCreatedAt($date);
            $actu->setUpdatedAt($date);

            $translationFr = new ActualityTranslation();
            $translationFr->setLocale('fr');
            $translationFr->setTitle('Super Actu #' . $i);
            $translationFr->setContent('Un incroyable contenu #' . $i);
            $translationFr->setSlug('super-actu-' . $i);

            $translationEn = new ActualityTranslation();
            $translationEn->setLocale('en');
            $translationEn->setTitle('Super News #' . $i);
            $translationEn->setContent('An incredible content #' . $i);
            $translationEn->setSlug('super-news-' . $i);

            $actu->addTranslation($translationFr);
            $actu->addTranslation($translationEn);

            $this->em->persist($actu);
        }

        // Create Project Team Members
        for ($i = 0; $i < self::NB_ELEM; $i++) {
            $imgMan = ''; // Mandatory (image is required) -- insert dummy image for men
            $imgWoman = ''; // Mandatory (image is required) -- insert dummy image for women
            $people = [$imgMan, $imgWoman];
            $image = array_rand($people);
            $member = new ProjectTeam();
            $member->setImage($people[$image]);
            $member->setName('Prénom Nom');
            $member->setWeight(rand(2, 40));
            $member->setCreatedBy($this->user);
            $member->setUpdatedBy($this->user);
            $member->setCreatedAt($date);
            $member->setUpdatedAt($date);

            $translationFr = new ProjectTeamTranslation();
            $translationFr->setLocale('fr');
            $translationFr->setRole('Lorem ipsum dolor sit amet, consectetur adipiscing elit FR');
            $translationFr->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
            totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. FR');
            $translationFr->setImgLicence('@inrae');

            $translationEn = new ProjectTeamTranslation();
            $translationEn->setLocale('en');
            $translationEn->setRole('Lorem ipsum dolor sit amet, consectetur adipiscing elit EN');
            $translationEn->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
            totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. EN');
            $translationEn->setImgLicence('@inrae');

            $member->addTranslation($translationFr);
            $member->addTranslation($translationEn);

            $this->em->persist($member);
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}

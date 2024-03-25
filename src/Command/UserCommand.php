<?php                                      
                                                     
namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends Command
{
    public const CMDNAME = 'app:user';

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     * Command configuration.
     */
    public function configure()
    {
        $this
            // The name of the command (the part after "bin/console")
            ->setName(self::CMDNAME)
            // The short description shown while running "php bin/console list"
            ->setDescription("User maintenance")
            // The full command description shown when running the command with the "--help" option
            ->setHelp("Depending options, this command can create user or update password..")
            // Configure an argument (email/username asking)
            ->addArgument('username', InputArgument::REQUIRED, 'The username to log in.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email address associated to.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password associated to.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'User Command',
            '============',
            ''
        ]);
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if (!$username) {
            $output->writeln("First argument Username is mandatory... And is actually missing.");
            return Command::INVALID;
        }
        if (!$email) {
            $output->writeln("Second argument Email is mandatory... And is actually missing.");
            return Command::INVALID;
        }
        if (!$password) {
            $output->writeln("Third argument Password is mandatory... And is actually missing.");
            return Command::INVALID;
        }
        $this->userService->createAdmin($username, $email, $password);
        $output->writeln('User : ' . $username . ' successfully created.');
        return Command::SUCCESS;
    }
}

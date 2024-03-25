<?php                                  
                                                     
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateImageUrlCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('app:update-image-url')
            ->setDescription('Update imageLocale field by retrieving data from another table')
            ->addArgument('sourceEntity', InputArgument::REQUIRED, 'Source entity class name')
            ->addArgument('targetEntity', InputArgument::REQUIRED, 'Target entity class name')
            ->addArgument('sourceId', InputArgument::REQUIRED, 'Source item ID')
            ->addArgument('targetId', InputArgument::REQUIRED, 'Target item ID');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceEntityClass = $input->getArgument('sourceEntity');
        $targetEntityClass = $input->getArgument('targetEntity');

        // Fetch entity repositories based on provided entity names
        $sourceRepository = $this->em->getRepository($sourceEntityClass);
        $targetRepository = $this->em->getRepository($targetEntityClass);
        $sourceId = $input->getArgument('sourceId');
        $targetId = $input->getArgument('targetId');

        // Fetch source and target entities by their IDs
        $sourceRecord = $sourceRepository->find($sourceId);
        $targetRecord = $targetRepository->find($targetId);

        if ($sourceRecord && $targetRecord) {
            $image = $sourceRecord->getImage();

            // Check if the image exists
            if ($image) {
                $targetRecord->setImageLocale($sourceRecord->getImage());
                
                $this->em->persist($targetRecord);
                $this->em->flush();

                $output->writeln('image_locale field updated successfully.');
            } else {
                $output->writeln('Source item has no image.');
            }
        } else {
            $output->writeln('Source or target item not found.');
        }

        return Command::SUCCESS;
    }
}

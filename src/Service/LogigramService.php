<?php                                      
                                                     
namespace App\Service;

use App\Entity\Logigram;
use Doctrine\ORM\EntityManagerInterface;

class LogigramService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function loadLogigram($logigram){

        $logigramSteps = [];
        
        if($logigram){
            foreach($logigram[0]->getLogigramSteps() as $step){

                $nextSteps = [];
    
                foreach($step->getLogigramNextSteps() as $nextStep){
                    $nextSteps[] = array(
                        'title' => $nextStep->getTitle(),
                        'info' => $nextStep->getInfo(),
                        'nextStep' => $nextStep->getNextStep(),
                    );
                };
    
                $choices = [];
    
                if ($step->getChoices() !== null){
                    foreach($step->getChoices() as $choice){
                        $choices[] = array(
                            $choice
                        );
                    }
                }
                
                $logigramSteps[] = array(
                    'title' => $step->getTitle(),
                    'info' => $step->getInfo(),
                    'choices' => $choices,
                    'nextSteps' => $nextSteps
                );
            };
    
            $data = [
                'title' => $logigram[0]->getTitle(),
                'subTitle' => $logigram[0]->getSubTitle(),
                'routeType' => $logigram[0]->getRouteType(),
                'isPublished' => $logigram[0]->getIsPublished(),
                'logigramSteps' => $logigramSteps
            ];
    
            return $data;
        }else {
            return[];
        }
    }

    /**
     * Generate a logigram
     * @param string $title
     */
    public function logigram(string $title)
    {
        $logigram = $this->em->getRepository(Logigram::class)->findByTitle($title);

        return $this->loadLogigram($logigram);
        
    }

    public function logigramByRoute(string $routeType){
        $logigram = $this->em->getRepository(Logigram::class)->findByRoute($routeType);

        return $this->loadLogigram($logigram);
    }

    public function logigramBySlug(string $targetSlug){
        $logigram = $this->em->getRepository(Logigram::class)->findBySlug($targetSlug);

        return $this->loadLogigram($logigram);
    }
}

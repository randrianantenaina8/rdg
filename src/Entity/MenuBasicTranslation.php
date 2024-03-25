<?php                                      
                                                     
namespace App\Entity;

use App\Repository\MenuBasicTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=MenuBasicTranslationRepository::class)
 */
class MenuBasicTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length label property.
     * Used by custom validator.
     */
    public const LEN_LABEL = 255;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $label;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function setLabel($label): self
    {
        $this->label = $label;

        return $this;
    }
}

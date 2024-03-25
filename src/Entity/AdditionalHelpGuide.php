<?php                                      
                                                     
namespace App\Entity;

use App\Repository\AdditionalHelpGuideRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Functional entity to link additionalHelp and guide with a weight property.
 *
 * @ORM\Entity(repositoryClass=AdditionalHelpGuideRepository::class)
 */
class AdditionalHelpGuide
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $weight = 10;

    /**
     * @var Guide
     *
     * @ORM\ManyToOne(targetEntity="Guide", inversedBy="additionalHelps")
     * @ORM\JoinColumn(name="guide_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $guide;

    /**
     * @var AdditionalHelp
     *
     * @ORM\ManyToOne(targetEntity="AdditionalHelp", inversedBy="relatedGuides")
     * @ORM\JoinColumn(name="additional_help_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $additionalHelp;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Guide|null
     */
    public function getGuide(): ?Guide
    {
        return $this->guide;
    }

    /**
     * @param Guide|null $guide
     *
     * @return $this
     */
    public function setGuide(?Guide $guide): self
    {
        $this->guide = $guide;

        return $this;
    }

    /**
     * @return AdditionalHelp|null
     */
    public function getAdditionalHelp(): ?AdditionalHelp
    {
        return $this->additionalHelp;
    }

    /**
     * @param AdditionalHelp|null $additionalHelp
     *
     * @return $this
     */
    public function setAdditionalHelp(?AdditionalHelp $additionalHelp): self
    {
        $this->additionalHelp = $additionalHelp;

        return $this;
    }

    public function __toString()
    {
        return 'item';
    }
}

<?php                                      
                                                     
namespace App\Entity;

use App\Entity\Lame\CenterMapLame;
use App\Repository\CenterMapCoordRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CenterMapCoordRepository::class)
 * @ORM\Table(name="center_map_coord")
 *
 * @RdgAssert\Constraint\CenterMapCoordConstraint()
 */
class CenterMapCoord
{
    public const LEN_NAME = 100;
    public const LEN_X = 20;
    public const LEN_Y = 20;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CenterMapLame
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Lame\CenterMapLame")
     * @ORM\JoinColumn(name="center_map_lame_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $centerLamina;

    /**
     * @var Institution|null
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $institution;

    /**
     * @var DataWorkshop|null
     *
     * @ORM\ManyToOne(targetEntity="DataWorkshop")
     * @ORM\JoinColumn(name="dataworkshop_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $dataworkshop;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=20)
     */
    private $x;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=20)
     */
    private $y;


    /**
     * Setter specific for ID created to be use by create and update in same controller.
     *
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return CenterMapLame|null
     */
    public function getCenterLamina(): ?CenterMapLame
    {
        return $this->centerLamina;
    }

    /**
     * @param CenterMapLame $centerLamina
     *
     * @return $this
     */
    public function setCenterLamina(CenterMapLame $centerLamina): self
    {
        $this->centerLamina = $centerLamina;

        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    /**
     * @param Institution|null $institution
     *
     * @return $this
     */
    public function setInstitution(?Institution $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return DataWorkshop|null
     */
    public function getDataworkshop(): ?DataWorkshop
    {
        return $this->dataworkshop;
    }

    /**
     * @param DataWorkshop|null $dataworkshop
     *
     * @return $this
     */
    public function setDataworkshop(?DataWorkshop $dataworkshop): self
    {
        $this->dataworkshop = $dataworkshop;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getX(): ?string
    {
        return $this->x;
    }

    /**
     * @param string $x
     *
     * @return $this
     */
    public function setX(string $x): self
    {
        $this->x = $x;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getY(): ?string
    {
        return $this->y;
    }

    /**
     * @param string $y
     *
     * @return $this
     */
    public function setY(string $y): self
    {
        $this->y = $y;

        return $this;
    }
}

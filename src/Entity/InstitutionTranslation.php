<?php                                      
                                                     
namespace App\Entity;

use App\Repository\InstitutionTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=InstitutionTranslationRepository::class)
 */
class InstitutionTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length acronym property.
     * Used by custom validator.
     */
    public const LEN_ACRONYM = 255;

    /**
     * Length extendedName property.
     * Used by custom validator.
     */
    public const LEN_EXT_NAME = 510;

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
     * @ORM\Column(type="string", length=255)
     */
    private $acronym;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=510)
     */
    private $extendedName;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * @param string $acronym
     *
     * @return $this
     */
    public function setAcronym($acronym): self
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtendedName()
    {
        return $this->extendedName;
    }

    /**
     * @param string $extendedName
     *
     * @return $this
     */
    public function setExtendedName($extendedName): self
    {
        $this->extendedName = $extendedName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }
}

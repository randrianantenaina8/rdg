<?php                                      
                                                     
namespace App\Entity;

use App\Repository\GlossaryTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=GlossaryTranslationRepository::class)
 */
class GlossaryTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length term property.
     * Used by custom validator.
     */
    public const LEN_TERM = 255;

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
    protected $term;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $definition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $plural;


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
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $term
     *
     * @return $this
     */
    public function setTerm($term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param string $definition
     *
     * @return $this
     */
    public function setDefinition($definition): self
    {
        $this->definition = $definition;

        return $this;
    }

    public function getPlural(): ?string
    {
        return $this->plural;
    }

    public function setPlural(?string $plural): self
    {
        $this->plural = $plural;

        return $this;
    }
}

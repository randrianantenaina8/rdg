<?php                                      
                                                     
namespace App\Entity;

use App\Repository\TaxonomyTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=TaxonomyTranslationRepository::class)
 * @ORM\Table(name="taxonomy_translation")
 */
class TaxonomyTranslation implements TranslationInterface
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
     * @ORM\Column(type="string", length=255)
     */
    protected $term;

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
}

<?php                                      
                                                     
namespace App\Entity;

use App\Repository\LogigramTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=LogigramTranslationRepository::class)
 * @ORM\Table(name="logigramTranslation")
 */
class LogigramTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length title property.
     * Used by custom validator.
     */
    public const LEN_TITLE = 255;

     /**
     * Length slug property.
     * Used by custom validator.
     */
    public const LEN_SLUG = 510;

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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=ActualityTranslation::LEN_SLUG, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $subTitle;

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
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return self
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     *
     * @return $this
     */
    public function setSubTitle($subTitle): self
    {
        $this->subTitle = $subTitle;

        return $this;
    }

}

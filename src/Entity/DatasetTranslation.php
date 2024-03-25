<?php                                      
                                                     
namespace App\Entity;

use App\Repository\DatasetTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DatasetTranslationRepository::class)
 */
class DatasetTranslation implements TranslationInterface
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
     * Length title property.
     * Used by custom validator.
     */
    public const LEN_IMG_LICENCE = 50;

    /**
     * Length slug property.
     * Used by custom validator.
     */
    public const LEN_IMG_LEGEND = 150;

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
     * @ORM\Column(type="string", length=self::LEN_TITLE)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageLocale;

    /**
     * Editorial hook.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $hook;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=self::LEN_SLUG, unique=true)
     */
    protected $slug;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=self::LEN_IMG_LICENCE, nullable=true)
     */
    protected $imgLicence;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=self::LEN_IMG_LEGEND, nullable=true)
     */
    protected $imgLegend;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageLocale()
    {
        return $this->imageLocale;
    }

    /**
     * @param $imageLocale
     *
     * @return $this
     */
    public function setImageLocale($imageLocale): self
    {
        $this->imageLocale = $imageLocale;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * @param string|null $hook
     *
     * @return $this
     */
    public function setHook($hook): self
    {
        $this->hook = $hook;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return self
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getImgLicence()
    {
        return $this->imgLicence;
    }

    /**
     * @param $imgLicence
     * 
     * @return $this
     */
    public function setImgLicence($imgLicence)
    {
        $this->imgLicence = $imgLicence;

        return $this;
    }

    /**
     * @return string
     */
    public function getImgLegend()
    {
        return $this->imgLegend;
    }

    /**
     * @param $imgLegend
     * 
     * @return $this
     */
    public function setImgLegend($imgLegend)
    {
        $this->imgLegend = $imgLegend;

        return $this;
    }
}

<?php                                      
                                                     
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\GuideTranslationRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=GuideTranslationRepository::class)
 */
class GuideTranslation implements TranslationInterface
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
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageLocale;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=510, unique=true)
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
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
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
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return $this
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getimgLicence()
    {
        return $this->imgLicence;
    }

    /**
     * @param $imgLicence
     * 
     * @return $this
     */
    public function setimgLicence($imgLicence)
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

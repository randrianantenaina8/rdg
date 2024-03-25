<?php                                      
                                                     
namespace App\Entity;

use App\Repository\ActualityDraftTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActualityDraftTranslationRepository::class)
 */
class ActualityDraftTranslation implements TranslationInterface
{
    use TranslationTrait;

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
     * @ORM\Column(type="string", length=ActualityTranslation::LEN_TITLE)
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
     * @ORM\Column(type="text")
     */
    protected $content;

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
     * @ORM\Column(type="string", length=50, nullable=true)
     * 
     * @Assert\Length(
     *    max=50
     * )
     */
    protected $imgLicence;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=150, nullable=true)
     * 
     * @Assert\Length(
     *     max=150
     * )
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

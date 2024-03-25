<?php                                      
                                                     
namespace App\Entity\Lame;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Lame
{
    /**
     * Length title common Translation's property.
     * Used by custom validator.
     */
    public const LEN_TITLE = 255;

    /**
     * Translation => class
     */
    public const TYPE = [
        'content.carousel' => CarouselLame::class,
        'content.spotlight' => SpotLightLame::class,
        'content.highlighted' => HighlightedLame::class,
        'content.centerlame' => CenterMapLame::class,
        'content.newsLame' => NewsLame::class,
    ];

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Find what kind of object's class it is.
     *
     * @var string
     */
    protected $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $weight = 10;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $isPublished;


    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $createdBy;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $updatedBy;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return $this
     */
    public function setWeight($weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $thisClass = get_class($this);

        foreach (self::TYPE as $label => $class) {
            if ($thisClass == $class) {
                return $label;
            }
        }
        return '';
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool|null $isPublished
     *
     * @return self
     */
    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime|null $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $createdBy
     *
     * @return self
     */
    public function setCreatedBy($createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param $updatedBy
     *
     * @return self
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}

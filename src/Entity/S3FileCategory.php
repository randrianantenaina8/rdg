<?php                                      
                                                     
namespace App\Entity;

use App\Repository\S3FileCategoryRepository;
use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=S3FileCategoryRepository::class)
 * @UniqueEntity("name")
 */
class S3FileCategory implements TimestampableRdgInterface
{
    use TimestampableRdgTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity=S3File::class, mappedBy="s3FileCategory", cascade={"persist"})
     */
    private $s3Files;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $createdBy;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $updatedBy;


    public function __construct()
    {
        $this->s3files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, S3File>
     */
    public function getS3Files(): Collection
    {
        return $this->s3Files;
    }

    public function addS3File(S3File $s3File): self
    {
        if (!$this->s3Files->contains($s3File)) {
            $this->s3Files[] = $s3File;
            $s3File->setS3FileCategory($this);
        }

        return $this;
    }

    public function removeS3File(S3File $s3File): self
    {
        if ($this->s3Files->removeElement($s3File)) {
            // set the owning side to null (unless already changed)
            if ($s3File->getS3FileCategory() === $this) {
                $s3File->setS3FileCategory(null);
            }
        }

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $createdBy
     *
     * @return $this
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
     * @return $this
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}

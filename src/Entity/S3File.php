<?php                                      
                                                     
namespace App\Entity;

use App\Repository\S3FileRepository;
use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=S3FileRepository::class)
 * @ORM\Table(name="s3file")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * 
 */
class S3File implements TimestampableRdgInterface
{
    use TimestampableRdgTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */ 
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="s3file", fileNameProperty="imageName", 
     * size="imageSize", mimeType="mimeType", originalName="originalName", dimensions="dimensions")
     * 
     * @Assert\File(
     *     maxSize="20M",
     *     mimeTypes={
     *          "image/png", 
     *          "image/jpg", 
     *          "image/jpeg", 
     *          "image/x-png",
     *          "image/webp",
     *          "application/pdf",
     *          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *          "application/vnd.oasis.opendocument.spreadsheet",
     *          "application/vnd.openxmlformats-officedocument.presentationml.presentation",
     *          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *          "application/vnd.oasis.opendocument.text",
     *          "text/x-markdown",
     *          "text/plain",
     *          "text/csv",
     *          "text/x-xsl"
     * })
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable="true")
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="string")
     * 
     * @var string
     */
    protected $originalName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $imageSize;

    /**
     * @ORM\Column(type="string", nullable="false")
     *
     * @var string
     */
    private $mimeType;

    /**
     * @var array<int, int>
     */
    protected $dimensions;

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

    /**
     * @ORM\ManyToOne(targetEntity=S3FileCategory::class, inversedBy="s3Files")
     * @ORM\JoinColumn(name="s3_file_category", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $s3FileCategory;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     *  @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setDimensions(?array $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    public function getS3FileCategory(): ?S3FileCategory
    {
        return $this->s3FileCategory;
    }

    public function setS3FileCategory(?S3FileCategory $s3FileCategory): self
    {
        $this->s3FileCategory = $s3FileCategory;

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
}

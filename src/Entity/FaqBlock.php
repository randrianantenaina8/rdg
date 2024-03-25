<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\FaqBlockRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=FaqBlockRepository::class)
 * @ORM\Table(name="faq_block")
 * @ORM\HasLifecycleCallbacks()
 *
 * @RdgAssert\Constraint\FaqBlockConstraint()
 */
class FaqBlock implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FaqHighlighted|null
     *
     * @ORM\OneToOne(targetEntity="FaqHighlighted", mappedBy="faq", orphanRemoval=true)
     */
    private $faqHighlighted;

    /**
     * @var ArrayCollection|HeadingFaq[]
     *
     * @ORM\OneToMany(targetEntity="HeadingFaq", mappedBy="faq", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $headings;

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
        $this->headings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return FaqHighlighted|null
     */
    public function getFaqHighlighted(): ?FaqHighlighted
    {
        return $this->faqHighlighted;
    }

    /**
     * @param FaqHighlighted|null $faqHighlighted
     *
     * @return $this
     */
    public function setFaqHighlighted(?FaqHighlighted $faqHighlighted): self
    {
        $this->faqHighlighted = $faqHighlighted;

        return $this;
    }

    /**
     * @return HeadingFaq[]|ArrayCollection
     */
    public function getHeadings()
    {
        return $this->headings;
    }

    public function addHeading(HeadingFaq $heading): self
    {
        if (!$this->headings->contains($heading)) {
            $this->headings[] = $heading;
            $heading->setFaq($this);
        }
        return $this;
    }

    public function removeHeading(HeadingFaq $heading): self
    {
        if ($this->headings->removeElement($heading)) {
            // set the owning side to null (unless already changed)
            if ($heading->getFaq() === $this) {
                $heading->setFaq(null);
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

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    /**
     * Magic method used in EasyAdmin BO list to get the translated properties.
     * Ex: when you list actualities, it will give you title, slug and locale.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        $arguments = [];
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * Magic method used when __get is not used.
     *
     * @param string $method
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->__get('title');
    }
}

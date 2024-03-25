<?php                                      
                                                     
namespace App\Entity;

use App\Repository\MenuMultipleRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MenuMultipleRepository::class)
 *
 * @RdgAssert\Constraint\MenuMultipleConstraint()
 */
class MenuMultiple implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=510, nullable=true)
     *
     * @Assert\Length(
     *     max=510
     * )
     */
    private $externalLink = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     max=255
     * )
     */
    private $systemLink = null;

    /**
     * @var Page|null
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="menuBasics")
     * @ORM\JoinColumn(name="page_link", referencedColumnName="id")
     */
    private $pageLink = null;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight = 10;

    /**
     * @var bool|int
     *
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $isActivated = false;

    /**
     * @var MenuMultiple|null
     *
     * @ORM\ManyToOne(targetEntity="MenuMultiple", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent = null;

    /**
     * @var ArrayCollection|MenuMultiple[]
     *
     * @ORM\OneToMany(targetEntity="MenuMultiple", mappedBy="parent", orphanRemoval=true)
     */
    private $childs;


    public function __construct()
    {
        $this->childs = new ArrayCollection();
    }

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
    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    /**
     * @param string|null $externalLink
     *
     * @return $this
     */
    public function setExternalLink(?string $externalLink): self
    {
        $this->externalLink = $externalLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSystemLink(): ?string
    {
        return $this->systemLink;
    }

    /**
     * @param string|null $systemLink
     *
     * @return $this
     */
    public function setSystemLink(?string $systemLink): self
    {
        $this->systemLink = $systemLink;

        return $this;
    }

    /**
     * @return Page|null
     */
    public function getPageLink(): ?Page
    {
        return $this->pageLink;
    }

    /**
     * @param Page|null $pageLink
     *
     * @return $this
     */
    public function setPageLink(?Page $pageLink): self
    {
        $this->pageLink = $pageLink;

        return $this;
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
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return bool|int
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * @param bool|int $isActivated
     *
     * @return $this
     */
    public function setIsActivated($isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * @return MenuMultiple|null
     */
    public function getParent(): ?MenuMultiple
    {
        return $this->parent;
    }

    /**
     * @param MenuMultiple|null $parent
     *
     * @return $this
     */
    public function setParent(?MenuMultiple $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return MenuMultiple[]|ArrayCollection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param MenuMultiple|null $child
     *
     * @return $this
     */
    public function addChild(MenuMultiple $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param MenuMultiple $child
     *
     * @return $this
     */
    public function removeChild(MenuMultiple $child): self
    {
        if ($this->childs->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
        return (string) $this->__get('label');
    }
}

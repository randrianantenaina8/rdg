<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\PageDraftRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=PageDraftRepository::class)
 * @ORM\Table(name="page_draft")
 * @ORM\HasLifecycleCallbacks()
 *
 * @RdgAssert\Constraint\PageConstraint(groups={"new", "edit"})
 */
class PageDraft implements TranslatableInterface, TimestampableRdgInterface
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
    protected $id;

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
     * @var Page|null
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $page;


    public function __construct()
    {
        $this->menuBasics = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Page|null
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * @param Page|null $page
     *
     * @return $this
     */
    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    public function getTitle()
    {
        return $this->proxyCurrentLocaleTranslation('getTitle');
    }

    public function getContent()
    {
        return $this->proxyCurrentLocaleTranslation('getContent');
    }

    public function getSlug()
    {
        return $this->proxyCurrentLocaleTranslation('getSlug');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $title = (string) $this->getTitle();

        if (empty($title)) {
            switch ($this->getCurrentLocale()) {
                case 'fr':
                    $title = $this->id . ' - Non traduit en français';
                    break;
                case 'en':
                    $title = $this->id . ' - Not translated in english';
                    break;
                default:
                    $title = $this->id . ' - Not translated at all';
                    break;
            }
        }

        return $title;
    }
}

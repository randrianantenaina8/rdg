<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\TaxonomyRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=TaxonomyRepository::class)
 * @ORM\Table(name="taxonomy")
 * @ORM\HasLifecycleCallbacks()
 *
 * @RdgAssert\Constraint\TaxonomyConstraint()
 */
class Taxonomy implements TranslatableInterface, TimestampableRdgInterface
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
     * @var Dataset[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="taxonomies")
     */
    private $datasets;

    /**
     * @var Event[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="taxonomies")
     */
    private $events;

    /**
     * @var Actuality[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Actuality", mappedBy="taxonomies")
     */
    private $actualities;


    public function __construct()
    {
        $this->datasets = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->actualities = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Dataset[]|ArrayCollection
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
     * @return Event[]|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return Actuality[]|ArrayCollection
     */
    public function getActualities()
    {
        return $this->actualities;
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
        return (string) $this->__get('term');
    }
}

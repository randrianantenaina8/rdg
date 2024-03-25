<?php                                      
                                                     
namespace App\Entity\Lame;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Entity\Institution;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Lame\CarouselLameRepository")
 * @ORM\Table(name="lame_carousel")
 *
 * @RdgAssert\Constraint\LaminaConstraint()
 */
class CarouselLame extends Lame implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * @var Institution[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Institution", inversedBy="carouselsLame")
     * @ORM\JoinTable(name="lame_carousels_centers")
     */
    public $entities;


    public function __construct()
    {
        $this->entities = new ArrayCollection();
    }

    /**
     * @return Institution[]|ArrayCollection
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param Institution $entity
     *
     * @return $this
     */
    public function addEntity($entity): self
    {
        if (!$this->entities->contains($entity)) {
            $this->entities->add($entity);
        }

        return $this;
    }

    /**
     * @param Institution $entity
     *
     * @return $this
     */
    public function removeEntity($entity): self
    {
        $this->entities->removeElement($entity);

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

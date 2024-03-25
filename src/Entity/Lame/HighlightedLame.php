<?php                                      
                                                     
namespace App\Entity\Lame;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Entity\Actuality;
use App\Entity\Dataset;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Lame\HighlightedLameRepository")
 * @ORM\Table(name="lame_highlighted")
 *
 * @RdgAssert\Constraint\LaminaConstraint()
 */
class HighlightedLame extends Lame implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset", inversedBy="highlightedLames")
     * @ORM\JoinColumn(name="dataset_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $dataset = null;


    /**
     * @return Dataset|null
     */
    public function getDataset(): ?Dataset
    {
        return $this->dataset;
    }

    /**
     * @param Dataset|null $dataset
     *
     * @return $this
     */
    public function setDataset(?Dataset $dataset): self
    {
        $this->dataset = $dataset;

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

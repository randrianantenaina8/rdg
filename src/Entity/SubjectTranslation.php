<?php                                      
                                                     
namespace App\Entity;

use App\Repository\SubjectTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=SubjectTranslationRepository::class)
 * @ORM\Table(name="contact_subject_translation")
 */
class SubjectTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length subject property.
     * Used by custom validator.
     */
    public const LEN_SUBJECT = 255;

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
     * @ORM\Column(type="string", length=255)
     */
    protected $subject;


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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}

<?php                                      
                                                     
namespace App\Service;

use App\Entity\GlossaryTranslation;
use Doctrine\ORM\EntityManagerInterface;

class MigrationUrlService
{
    /**
     * @var EntityManagerInterface
     */
    public $em;

    /**
     * @var string
     */
    public $urlSrc;

    /**
     * @var string
     */
    public $urlTarget;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, string $urlSrc, string $urlTarget)
    {
        $this->em = $em;
        $this->urlSrc = $urlSrc;
        $this->urlTarget = $urlTarget;
    }

    /**
     * Migrate wysiwyg content fields urls
     * 
     * @param mixed $entity
     * 
     * @return object
     */
    public function migrateUrls($entity)
    {
        $urlObjects = $this->em->getRepository($entity)->findByUrl($this->urlSrc);
        $nbElement = count($urlObjects);
   
        for ($i = 0 ; $i < $nbElement ; $i++) {
            $content = $urlObjects[$i]->getContent();
            $updatedContent = str_replace($this->urlSrc, $this->urlTarget, $content);
            $urlObjects[$i]->setContent($updatedContent);
        }

        $this->em->flush();

        return $nbElement;
    }

    /**
     * Migrate wysiwyg description fields urls
     * 
     * @param mixed $entity
     * 
     * @return object
     */
    public function migrateDescriptionUrls($entity)
    {
        $urlObjects = $this->em->getRepository($entity)->findByUrl($this->urlSrc);
        $nbElement = count($urlObjects);
   
        for ($i = 0 ; $i < $nbElement ; $i++) {
            $description = $urlObjects[$i]->getDescription();
            $updatedDescription = str_replace($this->urlSrc, $this->urlTarget, $description);
            $urlObjects[$i]->setDescription($updatedDescription);
        }

        $this->em->flush();

        return $nbElement;
    }

    /**
     * Migrate wysiswyg glossary urls
     * 
     * @return object
     */
    public function migrateGlossaryUrls()
    {
        $urlObjects = $this->em->getRepository(GlossaryTranslation::class)->findByUrl($this->urlSrc);
        $nbElement = count($urlObjects);
   
        for ($i = 0 ; $i < $nbElement ; $i++) {
            $definition = $urlObjects[$i]->getDefinition();
            $updatedDefinition = str_replace($this->urlSrc, $this->urlTarget, $definition);
            $urlObjects[$i]->setDefinition($updatedDefinition);
        }

        $this->em->flush();

        return $nbElement;
    }

    /**
     * Migrate external links of Basic and Multiple Menus
     * 
     * @param mixed $entity
     * 
     * @return object
     */
    public function migrateMenuUrls($entity)
    {
        $urlObjects = $this->em->getRepository($entity)->findByUrl($this->urlSrc);
        $nbElement = count($urlObjects);
   
        for ($i = 0 ; $i < $nbElement ; $i++) {
            $externalLink = $urlObjects[$i]->getExternalLink();
            $updatedExternalLink = str_replace($this->urlSrc, $this->urlTarget, $externalLink);
            $urlObjects[$i]->setExternalLink($updatedExternalLink);
        }

        $this->em->flush();

        return $nbElement;
    }

    /**
     * Migrate Cover Image links
     * 
     * @param mixed $entity
     * @param mixed $imageUrl
     * 
     * @return object
     */
    public function migrateImageUrls($entity, $imageUrl)
    {
        $urlObjects = $this->em->getRepository($entity)->findImageByUrl($this->urlSrc);
        $nbElement = count($urlObjects);
   
        for ($i = 0 ; $i < $nbElement ; $i++) {
            $imageUrl = $urlObjects[$i]->getImage();
            $updatedImageUrl = str_replace($this->urlSrc, $this->urlTarget, $imageUrl);
            $urlObjects[$i]->setImage($updatedImageUrl);
        }

        $this->em->flush();

        return $nbElement;
    }
}

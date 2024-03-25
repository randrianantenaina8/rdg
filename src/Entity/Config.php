<?php                                      
                                                     
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
    public const ROUTE = 'routes';

    /**
     * List all routes available with an HTML display.
     * List used in Introduction routeType's list to know on which controller an Intro banner should be displayed.
     */
    public const PAGE_ROUTES = [
        'front.header.home'      => 'front.homepage',
        'content.glossary'       => 'front.glossary',
        'content.actualities'    => 'front.actuality.list',
        'content.dataworkshops'  => 'front.dataworkshop.list',
        'content.institutions'   => 'front.institutions.list',
        'front.guide.homepage'   => 'front.guide.homepage',
        'content.events'         => 'front.event.list',
        'breadcrumbs.pastevents' => 'front.event.list.past',
        'breadcrumbs.nextevents' => 'front.event.list.next',
        'front.footer.contact'   => 'front.contact',
        'front.footer.contact'   => 'front.contact',
        'content.guide'          => 'front.guide.show',
        'content.team'           => 'front.repository.team.list',
        'content.data.repository'=> 'front.dataRepository.show',
        'content.disciplines'    => 'front.discipline.list'
    ];

    /**
     * List all system routes' name that can be used in different Front menus.
     * Used as a constraint validation in RouteType.
     */
    public const ROUTES = [
        'Homepage'       => 'front.homepage',
        'RSS'            => 'front.rss.all',
        'Glossary'       => 'front.glossary',
        'Actualities'    => 'front.actuality.list',
        'Data workshops' => 'front.dataworkshop.list',
        'Institutions'   => 'front.institutions.list',
        'Be accompanied' => 'front.guide.homepage',
        'Events'         => 'front.event.list',
        'Past events'    => 'front.event.list.past',
        'Future events'  => 'front.event.list.next',
        'Contact'        => 'front.contact',
        'Faq'            => 'front.faq.list',
        'Datasets'       => 'front.dataset.list',
        'Team'           => 'front.repository.team.list',
        'Disciplines'    => 'front.discipline.list'
    ];

    /**
     * Defined here all dynamic routes used to show an entity.
     *
     * List used by default when no show() method.
     * We can imagine an anchor to arrive directly on the good entity (and a where IN method to retrieve it).
     */
    public const ENTITY_ROUTES = [
        'event'          => 'front.event.show',
        'dataworkshop'   => 'front.dataworkshop.list',
        'institution'    => 'front.institutions.list',
        'actuality'      => 'front.actuality.show',
        'page'           => 'front.page.show',
        'guide'          => 'front.guide.show',
        'dataset'        => 'front.dataset.show',
        'team'           => 'front.repository.team.list',
        'repository'     => 'front.dataRepository.show',
        'disciplines'    => 'front.discipline.list'
    ];

    /**
     * Parameters to redirect to the fake 404 content not available in this language.
     */
    public const ROUTE_ERR_PARAMS = '--error-language-not-available--';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(
     *     min=1,
     *     max=255
     * )
     */
    private $name;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json")
     */
    private $data = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}

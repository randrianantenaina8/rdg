<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\EventTranslation;
use App\Field\Admin\TranslationField;
use App\Tool\DateTool;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class EventCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var \DateTime
     */
    protected $now;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param Security            $security
     */
    public function __construct(
        TranslatorInterface $translator,
        Security $security,
        AdminUrlGenerator $router
    )
    {
        $this->translator = $translator;
        $this->security = $security;
        $this->now = DateTool::dateAndTimeNow();
        $this->router = $router;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    /**
     * Added a default datetime only on creation form.
     * It can be removed by the user directly on the Create Form.
     *
     * @param string $entityFqcn
     *
     * @return Event|mixed
     *
     * @throws \Exception
     */
    public function createEntity(string $entityFqcn)
    {
        $event = parent::createEntity($entityFqcn);
        if ($event instanceof Event) {
            $now = DateTool::datetimeNow();
            $now->modify(sprintf('-%d seconds', (int)$now->format('s')));
            $event->setPublishedAt($now);
            $session = $this->getContext()->getRequest()->getSession();
            if (!$session->has('groupId')) {
                $session->set('groupId', uniqid());
            }
            $groupId = $session->get('groupId');
            $event->setGroupId($groupId);
            $datas = $this->getContext()->getRequest()->request->get('Event') ?? [];
            if (!empty($datas)) {
                $step = $datas['intervalle'] ?? null;
                $unit = $datas['periodicity'] ?? null;
                $sUnit = '';
                if ($unit == 'O') {
                    $sUnit = $unit;
                    $unit = 'D';
                }
                $start = !empty($datas['begin']) ? new \DateTime($datas['begin']) : null;
                $finish = !empty($datas['repetitionEndDate']) ? new \DateTime($datas['repetitionEndDate']) : null;
                $numberOccurrence = !empty($datas['numberOccurrence']) ? $datas['numberOccurrence'] : 0;

                if (!empty($step) && !empty($unit)) {
                    $interval = new DateInterval("P{$step}{$unit}");
                }
                if (!is_null($start) && !empty($interval) && !is_null($finish)) {
                    $period = new DatePeriod($start, $interval, $finish);
                }
                $datetime = new \DateTime();
                $publishedAt = !empty($datas['publishedAt']) ? new \DateTime($datas['publishedAt']) : null;
                $begin = !empty($datas['begin']) ? new \DateTime($datas['begin']) : null;
                $end = !empty($datas['end']) ? new \DateTime($datas['end']) : null;
                $repetitionEndDate = !empty($datas['repetitionEndDate']) ? new \DateTime(
                    $datas['repetitionEndDate']
                ) : null;
                $link = $datas['link'] ?? null;
                $titleFr = $datas['translations']['fr']['title'] ?? '';
                $hookFr = $datas['translations']['fr']['hook'] ?? '';
                $contentFr = $datas['translations']['fr']['content'] ?? '';
                $titleEn = $datas['translations']['en']['title'] ?? '';
                $hookEn = $datas['translations']['en']['hook'] ?? '';
                $contentEn = $datas['translations']['en']['content'] ?? '';
                $cBegin = clone $begin;
                $cEnd = is_object($end) ? clone $end : null;
                $duration = is_object($cEnd) ? $cBegin->diff($cEnd) : null;
                $holiday = [];
                $date1 = clone $start;
                if (!is_null($repetitionEndDate) && !empty($period)) {
                    foreach ($period as $key => $date) {
                        if ($key >= 1) {
                            if (
                                $sUnit != 'O'
                                || (
                                    $sUnit === 'O'
                                    && $date->format("N") < 6
                                    && !in_array($date->format("Y-m-d"), $holiday)
                                )
                            ) {
                                $this->createEvent(
                                    $date,
                                    $publishedAt,
                                    $duration,
                                    $repetitionEndDate,
                                    $numberOccurrence,
                                    $datetime,
                                    $link,
                                    $step,
                                    $unit,
                                    $titleFr,
                                    $hookFr,
                                    $contentFr,
                                    $titleEn,
                                    $hookEn,
                                    $contentEn,
                                    $groupId
                                );
                            }
                        }
                    }
                } elseif (!empty($numberOccurrence)) {
                    for ($i = 1; $i < $numberOccurrence; $i++) {
                        $date1->add($interval);
                        $date = clone $date1;
                        if (
                            $sUnit != 'O'
                            || (
                                $sUnit === 'O'
                                && $date->format("N") < 6
                                && !in_array($date->format("Y-m-d"), $holiday)
                            )
                        ) {
                            $this->createEvent(
                                $date,
                                $publishedAt,
                                $duration,
                                $repetitionEndDate,
                                $numberOccurrence,
                                $datetime,
                                $link,
                                $step,
                                $unit,
                                $titleFr,
                                $hookFr,
                                $contentFr,
                                $titleEn,
                                $hookEn,
                                $contentEn,
                                $groupId
                            );
                        }
                    }
                }
            }
        }

        return $event;
    }

    /**
     * Added a default datetime only on creation form.
     * It can be removed by the user directly on the Create Form.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Event) {
            return;
        }
        $datas = $this->getContext()->getRequest()->request->get('Event') ?? [];
        if (!empty($datas)) {
            $entityInstance->setMassModification(0);
            $massModification = $datas['massModification'] ?? null;
            if ($massModification == 1) {
                $this->updateEvents($datas, $entityInstance, $entityManager);
            } else {
                $entityInstance->setGroupId(uniqid());
                $entityInstance->setIntervalle(null);
                parent::updateEntity($entityManager, $entityInstance);
            }
        }
    }

    /**
     * Added a default datetime only on creation form.
     * It can be removed by the user directly on the Create Form.
     *
     * @param datetime $date
     * @param datetime $publishedAt
     * @param datetime $duration
     * @param datetime $repetitionEndDate
     * @param int      $numberOccurrence
     * @param datetime $datetime
     * @param string   $link
     * @param int      $step
     * @param string   $unit
     * @param string   $titleFr
     * @param string   $hookFr
     * @param string   $contentFr
     * @param string   $titleEn
     * @param string   $hookEn
     * @param string   $contentEn
     *
     * @return Event|mixed
     */
    public function createEvent(
        $date,
        $publishedAt,
        $duration,
        $repetitionEndDate,
        $numberOccurrence,
        $datetime,
        $link,
        $step,
        $unit,
        $titleFr,
        $hookFr,
        $contentFr,
        $titleEn,
        $hookEn,
        $contentEn,
        $groupId
    ) {
        $event = new Event();
        $cDate = clone $date;
        $event->setPublishedAt($publishedAt);
        $event->setBegin($date);
        $cDate = clone $date;
        if (!empty($duration)) {
            $event->setEnd($cDate->add($duration));
        }
        $event->setIntervalle($step);
        $event->setPeriodicity($unit);
        $event->setRepetitionEndDate($repetitionEndDate);
        $event->setNumberOccurrence($numberOccurrence);
        $event->setLink($link);
        $event->setCreatedAt($datetime);
        $event->setUpdatedAt($datetime);
        $event->setCreatedBy($this->security->getUser());
        $event->setUpdatedBy($this->security->getUser());
        $event->setGroupId($groupId);
        $translation1 = new EventTranslation();
        $translation1->setTitle($titleFr);
        $translation1->setHook($hookFr);
        $translation1->setContent($contentFr);
        $translation1->setLocale('fr');
        $translation1->setTranslatable($event);
        $translation2 = new EventTranslation();
        $translation2->setTitle($titleEn);
        $translation2->setHook($hookEn);
        $translation2->setContent($contentEn);
        $translation2->setLocale('en');
        $translation2->setTranslatable($event);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($translation1);
        $entityManager->persist($translation2);
        $entityManager->persist($event);
        return $event;
    }

    /**
     * Added a default datetime only on creation form.
     *
     * @param array                  $datas
     * @param Event                  $entityInstance
     * @param EntityManagerInterface $entityManager
     */
    public function updateEvents($datas, $entityInstance, $entityManager)
    {
        $groupId = $entityInstance->getGroupId();
        $repository = $entityManager->getRepository(Event::class);
        $events = $repository->findBy(['group_id' => $groupId]);
        $link = $datas['link'] ?? null;
        $titleFr = $datas['translations']['fr']['title'] ?? '';
        $hookFr = $datas['translations']['fr']['hook'] ?? '';
        $contentFr = $datas['translations']['fr']['content'] ?? '';
        $titleEn = $datas['translations']['en']['title'] ?? '';
        $hookEn = $datas['translations']['en']['hook'] ?? '';
        $contentEn = $datas['translations']['en']['content'] ?? '';
        foreach ($events as $event) {
            $translations = $event->getTranslations();
            foreach ($translations as $translation) {
                if ($translation->getLocale() == "fr") {
                    $translation->setTitle($titleFr);
                    $translation->setHook($hookFr);
                    $translation->setContent($contentFr);
                } elseif ($translation->getLocale() == "en") {
                    $translation->setTitle($titleEn);
                    $translation->setHook($hookEn);
                    $translation->setContent($contentEn);
                }
            }
            $event->setMassModification(0);
            $event->setLink($link);
        }
        $entityManager->flush();
    }


    /**
     * Add Customize wysiwyg them to forms.
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $event = $this->translator->trans('content.event');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.events')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $event)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $event)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.slug', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * By default, sort ASC events by title in current locale.
     *
     * @param SearchDto        $searchDto
     * @param EntityDto        $entityDto
     * @param FieldCollection  $fields
     * @param FilterCollection $filters
     *
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $alias = $qb->getRootAliases()[0];
        $qb->leftJoin($alias . '.translations', 'tt', Join::WITH, 'tt.locale = :locale')
            ->setParameter('locale', $this->getContext()->getRequest()->getLocale())
            ->addOrderBy('tt.title', 'ASC');
        return $qb;
    }

    /**
     * @param AdminContext $context
     *
     * @return \EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(AdminContext $context)
    {
        $session = $this->getContext()->getRequest()->getSession();
        $session->remove('groupId');

        return parent::index($context);
    }

    /**
     * Duplicate Event
     *
     * @param AdminContext            $context
     * @param EntityManagerInterface  $entityManager
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * 
     * @throws \Exception
     */
    public function duplicate(AdminContext $context, EntityManagerInterface $entityManager)
    {
        // Get the current item
        $event = $context->getEntity()->getInstance();
        $message = $this->translator->trans('content.clone.success');

        try {
            // Create a new instance of the entity
            $clonedEvent = clone $event;

            $clonedEvent->setUpdatedBy($this->security->getUser());
            $clonedEvent->setCreatedBy($this->security->getUser());
            $clonedEvent->setCreatedAt($this->now);
            $clonedEvent->setUpdatedAt($this->now);
            $clonedEvent->setPublishedAt(null);

            /** @var EventTranslation $eventTranslation */
            foreach ($event->getTranslations() as $eventTranslation) {
                $translation = new EventTranslation();
                $translation->setLocale($eventTranslation->getLocale());
                $translation->setTitle($eventTranslation->getTitle() . '_Copy');
                $translation->setHook($eventTranslation->getHook());
                $translation->setContent($eventTranslation->getContent());
                $translation->setSlug($eventTranslation->getSlug());

                $clonedEvent->addTranslation($translation);
            }

            /** @var Taxonomy $taxonomy */
            foreach ($event->getTaxonomies() as $taxonomy) {
                $clonedEvent->addTaxonomy($taxonomy);
            }
            
            // Save the cloned item to the database
            $entityManager->persist($clonedEvent);
            $entityManager->flush();            
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }

        // Redirect back to the index page
        $urlToReturn = $this->router
            ->setController(EventCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($clonedEvent->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }

    /**
     * Set permissions on CRUD actions.
     * Customize label on create button.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Duplicate item
        $duplicate = Action::new('duplicate', $this->translator->trans('content.clone.button'))
            ->linkToCrudAction('duplicate')
            ->setIcon('fa fa-copy')
            ->setCssClass('btn');
        
        $actions = parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_CONTRIB',
                Action::EDIT   => 'ROLE_CONTRIB',
                Action::DELETE => 'ROLE_CONTRIB',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.event'))
                );
            })
            ->add(Crud::PAGE_INDEX, $duplicate)
        ;

        return $actions;
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $fieldsConfig = [
            'title'   => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('event.prop.title')),
                'attr'       => ['maxlength' => EventTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => EventTranslation::LEN_TITLE]
                ),
            ],
            'hook'    => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('event.prop.hook')),
                'attr'       => ['maxlength' => EventTranslation::LEN_HOOK],
                'help'       => $this->translator->trans('event.prop.hook.help') .
                    ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => EventTranslation::LEN_HOOK]),
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('event.prop.content')),
            ],
            'slug'    => [
                'field_type' => SlugType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('prop.slug')),
                'attr'       => ['maxlength' => EventTranslation::LEN_SLUG],
                'target'     => 'title',
                'help'       => $this->translator->trans('prop.slug.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => EventTranslation::LEN_SLUG]),
            ],
        ];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true)
                ->setFormTypeOptions(
                    [
                        'attr' => [
                            'helper' => [
                                $this->translator->trans('prop.translations.help.one'),
                            ]
                        ]
                    ]
                );

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.published'))
                ->setHelp($this->translator->trans('prop.group.published.help'));
            $fields[] = DateTimeField::new('publishedAt', ucfirst($this->translator->trans('prop.publishedAt')));

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'))
                ->setHelp($this->translator->trans('prop.group.additional.help'));
            $fields[] = UrlField::new('link', ucfirst($this->translator->trans('event.prop.link')));
            $fields[] = DateTimeField::new('begin', ucfirst($this->translator->trans('event.prop.begin')))
                ->setHelp($this->translator->trans('date.format.help'))
                ->setColumns(6);
            $fields[] = DateTimeField::new('end', ucfirst($this->translator->trans('event.prop.end')))
                ->setHelp($this->translator->trans('date.format.help'))
                ->setColumns(6);
            $fields[] = AssociationField::new('taxonomies', ucfirst($this->translator->trans('content.taxonomy')));

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.repeattime'))
                ->setHelp($this->translator->trans('prop.group.repeattime.help'));
            $fields[] = IntegerField::new('intervalle', ucfirst($this->translator->trans('event.prop.intervalle')))
                ->setColumns(6);
            $fields[] = ChoiceField::new('periodicity', ucfirst($this->translator->trans('event.prop.periodicity')))
                ->setChoices($this->getPeriodes())
                ->setColumns(6);
            $fields[] = DateField::new(
                'repetitionEndDate',
                ucfirst($this->translator->trans('event.prop.repetitionEndDate'))
            )
                ->setHelp($this->translator->trans('simpleDate.format.help'))
                ->setColumns(6);
            $fields[] = IntegerField::new(
                'numberOccurrence',
                $this->translator->trans('event.prop.numberOccurrence')
            )
                ->setHelp($this->translator->trans('event.prop.numberOccurrence.help'))
                ->setColumns(6);

            $fields[] = HiddenField::new('groupId', "groupId");
        } elseif ($pageName === Crud::PAGE_EDIT) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.group.editEvent')))
                ->setHelp($this->translator->trans('prop.group.editEvent.help'));
            $currentEntity = $this->getContext()->getEntity()->getInstance();
            if (!empty($currentEntity->getIntervalle())){
                $fields[] = BooleanField::new('massModification', ucfirst($this->translator->trans('event.prop.groupe')));
            }
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true)
                ->setFormTypeOptions(
                    [
                        'attr' => [
                            'helper' => [
                                $this->translator->trans('prop.translations.help.one'),
                            ]
                        ]
                    ]
                );

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.published'))
                ->setHelp($this->translator->trans('prop.group.published.help'));
            $fields[] = DateTimeField::new('publishedAt', ucfirst($this->translator->trans('prop.publishedAt')));

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'))
                ->setHelp($this->translator->trans('prop.group.additional.help'));
            $fields[] = UrlField::new('link', ucfirst($this->translator->trans('event.prop.link')));
            $fields[] = DateTimeField::new('begin', ucfirst($this->translator->trans('event.prop.begin')))
                ->setHelp($this->translator->trans('date.format.help'))
                ->setColumns(6);
            $fields[] = DateTimeField::new('end', ucfirst($this->translator->trans('event.prop.end')))
                ->setHelp($this->translator->trans('date.format.help'))
                ->setColumns(6);
            $fields[] = AssociationField::new('taxonomies', ucfirst($this->translator->trans('content.taxonomy')));

            $fields[] = HiddenField::new('groupId', "groupId");
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('event.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = DateTimeField::new('begin', ucfirst($this->translator->trans('event.prop.begin')))
                ->setCustomOption(DateTimeField::OPTION_TIME_PATTERN, DateTimeField::FORMAT_SHORT);
            $fields[] = DateTimeField::new('end', ucfirst($this->translator->trans('event.prop.end')))
                ->setCustomOption(DateTimeField::OPTION_TIME_PATTERN, DateTimeField::FORMAT_SHORT);
            $fields[] = DateTimeField::new('publishedAt', ucfirst($this->translator->trans('prop.publishedAt')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * @return string[]
     */
    protected function getPeriodes()
    {
        $periodes = [
            $this->translator->trans('event.prop.semaines')  => 'W',
            $this->translator->trans('event.prop.mois')      => 'M',
            $this->translator->trans('event.prop.ans')       => 'Y',
            $this->translator->trans('event.prop.ouvrables') => 'O',
            $this->translator->trans('event.prop.jours')     => 'D'
        ];

        return $periodes;
    }

    /**
     * @return int[]
     */
    protected function getEditModes()
    {
        $modes = [
            $this->translator->trans('event.prop.individuel') => 1,
            $this->translator->trans('event.prop.groupe')     => 2
        ];

        return $modes;
    }
}

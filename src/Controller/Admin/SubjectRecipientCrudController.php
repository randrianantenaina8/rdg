<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Recipient;
use App\Entity\Subject;
use App\Entity\SubjectRecipient;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class SubjectRecipientCrudController extends AbstractCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return SubjectRecipient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $recipients = $this->getRecipients();
        $subjects = $this->getSubjects();
        $fields[] = AssociationField::new('subject', ucfirst($this->translator->trans('content.contact.subject')));
        $fields[] = AssociationField::new('recipient', ucfirst($this->translator->trans('content.contact.recipients')));
        
        return $fields;
    }

    /**
     * Retrieve recipient list.
     *
     * @return array
     */
    public function getRecipients()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $toRecipients = $entityManager->getRepository(Recipient::class)->findAll();
        $tRecipients = [];
        
        foreach ($toRecipients as $name => $recipient) {
            $tRecipients[$recipient->getEmail()] = $recipient->getEmail() ;
        }
        
        return $tRecipients;
    }

    /**
     * Retrieve subject list.
     *
     * @return array
     */
    public function getSubjects()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $toSubjects = $entityManager->getRepository(Subject::class)->findAll();
        $tSubjects = [];
        
        foreach ($toSubjects as $name => $subject) {
            $tSubjects[$subject->getSubject()] = $subject->getSubject() ;
        }
        
        return $tSubjects;
    }
}

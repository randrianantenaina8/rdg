<?php

namespace App\Service;

use App\Entity\ProjectTeam;
use App\Entity\ProjectTeamDraft;
use App\Entity\ProjectTeamDraftTranslation;
use App\Entity\ProjectTeamTranslation;
use App\Tool\DateTool;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProjectTeamService
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \DateTime
     */
    protected $now;

    /**
     * @var array
     */
    protected $locales;


    public function __construct(
        $locales,
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
        $this->now = DateTool::dateAndTimeNow();
    }

    /**
     * Publish a ProjectTeamDraft ie create/update a Member associated to the ProjectTeamDraft object.
     *
     * @param ProjectTeamDraft $draft
     */
    public function publish(ProjectTeamDraft $draft)
    {
        if ($draft->getMember()) {
            $this->updateMember($draft);
            $this->deleteDraft($draft);
            return;
        }
        $this->createMember($draft);
        $this->deleteDraft($draft);
    }

    /**
     * Update Member properties with ProjectTeamDraft properties.
     *
     * @param ProjectTeamDraft $draft
     */
    public function updateMember(ProjectTeamDraft $draft)
    {
        $member = $draft->getMember();
        $member->setName($draft->getName());
        $member->setWeight($draft->getWeight());
        $member->setUpdatedAt($this->now);
        $member->setUpdatedBy($this->security->getUser());
        // Copy file from draft.
        if ($draft->getImage()) {
            $member->setImage($draft->getImage());
        }

        /** @var ProjectTeamTranslation $translation */
        foreach ($member->getTranslations() as $translation) {
            $member->removeTranslation($translation);
            $this->em->remove($translation);
        }

        $this->em->flush();

        /** @var ProjectTeamDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new ProjectTeamTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setRole($draftTranslation->getRole());
            $translation->setDescription($draftTranslation->getDescription());
            $translation->setImgLicence($draftTranslation->getImgLicence());

            $member->addTranslation($translation);
        }

        // Save last modifications.
        $this->em->flush();
    }

    /**
     * Create a Member from a ProjectTeamDraft then associated it to the ProjectTeamDraft object.
     *
     * @param ProjectTeamDraft $draft
     */
    public function createMember(ProjectTeamDraft $draft)
    {
        $member = new ProjectTeam();

        $member->setName($draft->getName());
        $member->setWeight($draft->getWeight());
        if ($draft->getImage()) {
            $member->setImage($draft->getImage());
        }
        $member->setUpdatedBy($this->security->getUser());
        $member->setCreatedBy($this->security->getUser());
        $member->setCreatedAt($this->now);
        $member->setUpdatedAt($this->now);

        /** @var ProjectTeamDraftTranslation $draftTranslation */
        foreach ($draft->getTranslations() as $draftTranslation) {
            $translation = new ProjectTeamTranslation();
            $translation->setLocale($draftTranslation->getLocale());
            $translation->setRole($draftTranslation->getRole());
            $translation->setDescription($draftTranslation->getDescription());
            $translation->setImgLicence($draftTranslation->getImgLicence());

            $member->addTranslation($translation);
        }

        $this->em->persist($member);
        $this->em->flush();
    }

    /**
     * Deleting the draft when it is published.
     * @param ProjectTeamDraft $draft
     */
    public function deleteDraft(ProjectTeamDraft $draft)
    {
        $this->em->remove($draft);
        $this->em->flush();
    }

    /**
     * Find a MemberDraft associated to a Member or create a new one from ProjectTeam object.
     *
     * @param ProjectTeam $member
     *
     * @return ProjectTeamDraft
     */
    public function findOrCreateDraft(ProjectTeam $member)
    {
        $memberDraft = $this->em->getRepository(ProjectTeamDraft::class)->findOneCompleteByMember($member);

        if (!$memberDraft instanceof ProjectTeamDraft) {
            $memberDraft = new ProjectTeamDraft();
            $memberDraft->setName($member->getName());
            $memberDraft->setWeight($member->getWeight());
            $memberDraft->setUpdatedBy($this->security->getUser());
            $memberDraft->setCreatedBy($this->security->getUser());
            $memberDraft->setCreatedAt($this->now);
            $memberDraft->setUpdatedAt($this->now);
            $memberDraft->setMember($member);
            if ($member->getImage()) {
                $memberDraft->setImage($member->getImage());
            }

            /** @var ProjectTeamTranslation $memberTranslation */
            foreach ($member->getTranslations() as $memberTranslation) {
                $translation = new ProjectTeamDraftTranslation();
                $translation->setLocale($memberTranslation->getLocale());
                $translation->setRole($memberTranslation->getRole());
                $translation->setDescription($memberTranslation->getDescription());
                $translation->setImgLicence($memberTranslation->getImgLicence());

                $memberDraft->addTranslation($translation);
            }

            $this->em->persist($memberDraft);
            $this->em->flush();
        }

        return $memberDraft;
    }
}

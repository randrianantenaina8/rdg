<?php                                      
                                                     
namespace Dipso\LockeditBundle;

use Dipso\LockeditBundle\Entity\Lock;
use Dipso\LockeditBundle\Repository\LockRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class Lockedit
{
    private lockRepository $lockRepository;
    private $em;
    private int $ttl;
    private Lock $lock;

    public function __construct(ManagerRegistry $manager, int $ttl)
    {
        $this->em = $manager->getManagerForClass(Lock::class);
        $this->lockRepository = new LockRepository($manager);
        $this->ttl = $ttl;
        $this->releaseOldLock();
    }


    public function setLock(string $userId, string $keyName): void
    {
        $lock = new Lock();
        $lock->setUser($userId);
        $lock->setResource($keyName);
        $this->lock = $lock;
    }

    public function saveLock()
    {
        if (isset($this->lock)) {
            $this->lock->setTimestamp(new \DateTime('now'));
            $this->em->persist($this->lock);
            $this->em->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function releaseLock()
    {
        if (isset($this->lock)) {
            $this->lockRepository->remove($this->lock, true);
        }
    }

    public function releaseOldLock()
    {
        $obsoleteLocks = $this->lockRepository->matching(self::getObsoleteCriteria($this->ttl));
        foreach ($obsoleteLocks as $obsoleteLock) {
            $this->em->remove($obsoleteLock);
        }
        $this->em->flush();
    }

    public function releaseLockByUserId(string $userId)
    {
        $userLocks = $this->lockRepository->matching(self::getUserCriteria($userId));
        foreach ($userLocks as $userLock) {
            $this->em->remove($userLock);
        }
        $this->em->flush();
    }

    public function isAvalaible(): bool
    {
        if (!$this->isMine() && $this->isTaken()) {
            return false;
        }
        return true;
    }

    public function isMine(): bool
    {
        $criteria = new Criteria();
        $criteria->where($criteria::expr()->eq('resource', $this->lock->getResource()));
        $criteria->andWhere($criteria::expr()->eq('user', $this->lock->getUser()));

        $result = $this->lockRepository->matching($criteria);

        if (empty($result)) {
            $this->lock = $result[0];
            return true;
        } else {
            return false;
        }
    }

    public function isTaken(): bool
    {
        $criteria = new Criteria();
        $criteria->where($criteria::expr()->eq('resource', $this->lock->getResource()));
        $criteria->andWhere($criteria::expr()->neq('user', $this->lock->getUser()));

        $result = $this->lockRepository->matching($criteria);
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    public static function getObsoleteCriteria($ttl): Criteria
    {
        $criteria = new Criteria();
        $datetime = new \DateTime('now');
        $datetime->modify('- ' . $ttl . ' seconds');
        $criteria->andWhere(
            $criteria::expr()->lte('timestamp', $datetime)
        );

        return $criteria;
    }

    public static function getUserCriteria($userId): Criteria
    {
        $criteria = new Criteria();
        $criteria->andWhere(
            $criteria::expr()->eq('user', $userId)
        );

        return $criteria;
    }
}

<?php

namespace App\Repository;

use App\Entity\PomodoroSession;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PomodoroSession>
 */
class PomodoroSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PomodoroSession::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.startTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTodaySessionsByUser(User $user): array
    {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');

        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.startTime >= :today')
            ->andWhere('p.startTime < :tomorrow')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->orderBy('p.startTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatisticsByUser(User $user): array
    {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');
        $weekStart = new \DateTimeImmutable('monday this week');
        $weekEnd = $weekStart->modify('+7 days');

        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as totalSessions')
            ->addSelect('SUM(p.duration) as totalTime')
            ->addSelect('SUM(CASE WHEN p.completed = true THEN 1 ELSE 0 END) as completedSessions')
            ->addSelect('SUM(CASE WHEN p.startTime >= :today AND p.startTime < :tomorrow THEN 1 ELSE 0 END) as todaySessions')
            ->addSelect('SUM(CASE WHEN p.startTime >= :weekStart AND p.startTime < :weekEnd THEN 1 ELSE 0 END) as weekSessions')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->setParameter('weekStart', $weekStart)
            ->setParameter('weekEnd', $weekEnd)
            ->getQuery()
            ->getSingleResult();

        // Calculate first session date for averagePerDay
        $firstSession = $this->createQueryBuilder('p')
            ->select('MIN(p.startTime) as firstDate')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        $averagePerDay = 0;
        if ($firstSession) {
            $firstDate = new \DateTimeImmutable($firstSession);
            $daysSinceFirst = max(1, $today->diff($firstDate)->days + 1);
            $averagePerDay = round($qb['totalSessions'] / $daysSinceFirst, 1);
        }

        return [
            'totalSessions' => (int) $qb['totalSessions'],
            'totalTime' => (int) ($qb['totalTime'] ?? 0),
            'completedSessions' => (int) $qb['completedSessions'],
            'todaySessions' => (int) $qb['todaySessions'],
            'weekSessions' => (int) $qb['weekSessions'],
            'averagePerDay' => (float) $averagePerDay,
        ];
    }
}

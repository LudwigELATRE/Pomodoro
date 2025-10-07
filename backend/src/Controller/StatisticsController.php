<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PomodoroSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/statistics')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private PomodoroSessionRepository $pomodoroRepository
    ) {}

    #[Route('', methods: ['GET'])]
    public function getStatistics(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $stats = $this->pomodoroRepository->getStatisticsByUser($user);

        return $this->json([
            'totalSessions' => $stats['totalSessions'],
            'completedSessions' => $stats['completedSessions'],
            'totalTime' => $stats['totalTime'],
            'totalTimeFormatted' => $this->formatTime($stats['totalTime']),
            'todaySessions' => $stats['todaySessions'],
            'weekSessions' => $stats['weekSessions'],
            'averagePerDay' => $stats['averagePerDay'],
        ]);
    }

    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return sprintf('%dh %dm', $hours, $minutes);
    }
}

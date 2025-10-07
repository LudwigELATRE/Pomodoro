<?php

namespace App\Controller;

use App\Entity\PomodoroSession;
use App\Entity\User;
use App\Repository\PomodoroSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pomodoros')]
class PomodoroController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PomodoroSessionRepository $pomodoroRepository
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['duration']) || !isset($data['type'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $session = new PomodoroSession();
        $session->setUser($user);
        $session->setStartTime(new \DateTimeImmutable($data['startTime'] ?? 'now'));
        $session->setDuration($data['duration']);
        $session->setType($data['type']);
        $session->setCompleted($data['completed'] ?? false);

        if (isset($data['endTime'])) {
            $session->setEndTime(new \DateTimeImmutable($data['endTime']));
        }

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $this->json([
            'id' => $session->getId(),
            'startTime' => $session->getStartTime()->format('Y-m-d H:i:s'),
            'endTime' => $session->getEndTime()?->format('Y-m-d H:i:s'),
            'duration' => $session->getDuration(),
            'type' => $session->getType(),
            'completed' => $session->isCompleted(),
        ], 201);
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $sessions = $this->pomodoroRepository->findByUser($user);

        return $this->json(array_map(function (PomodoroSession $session) {
            return [
                'id' => $session->getId(),
                'startTime' => $session->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $session->getEndTime()?->format('Y-m-d H:i:s'),
                'duration' => $session->getDuration(),
                'type' => $session->getType(),
                'completed' => $session->isCompleted(),
            ];
        }, $sessions));
    }

    #[Route('/today', methods: ['GET'])]
    public function today(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $sessions = $this->pomodoroRepository->findTodaySessionsByUser($user);

        return $this->json(array_map(function (PomodoroSession $session) {
            return [
                'id' => $session->getId(),
                'startTime' => $session->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $session->getEndTime()?->format('Y-m-d H:i:s'),
                'duration' => $session->getDuration(),
                'type' => $session->getType(),
                'completed' => $session->isCompleted(),
            ];
        }, $sessions));
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $session = $this->pomodoroRepository->find($id);

        if (!$session || $session->getUser() !== $user) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['completed'])) {
            $session->setCompleted($data['completed']);
        }

        if (isset($data['endTime'])) {
            $session->setEndTime(new \DateTimeImmutable($data['endTime']));
        }

        $this->entityManager->flush();

        return $this->json([
            'id' => $session->getId(),
            'startTime' => $session->getStartTime()->format('Y-m-d H:i:s'),
            'endTime' => $session->getEndTime()?->format('Y-m-d H:i:s'),
            'duration' => $session->getDuration(),
            'type' => $session->getType(),
            'completed' => $session->isCompleted(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $session = $this->pomodoroRepository->find($id);

        if (!$session || $session->getUser() !== $user) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        $this->entityManager->remove($session);
        $this->entityManager->flush();

        return $this->json(['message' => 'Session deleted'], 200);
    }
}

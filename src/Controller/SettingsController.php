<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', methods: ['GET'])]
    public function getSettings(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $settings = $user->getSettings();

        if (!$settings) {
            $settings = new UserSettings();
            $settings->setUser($user);
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
        }

        return $this->json([
            'workDuration' => $settings->getWorkDuration(),
            'shortBreakDuration' => $settings->getShortBreakDuration(),
            'longBreakDuration' => $settings->getLongBreakDuration(),
            'pomodorosUntilLongBreak' => $settings->getPomodorosUntilLongBreak(),
        ]);
    }

    #[Route('', methods: ['PUT'])]
    public function updateSettings(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $settings = $user->getSettings();

        if (!$settings) {
            $settings = new UserSettings();
            $settings->setUser($user);
            $this->entityManager->persist($settings);
        }

        $data = json_decode($request->getContent(), true);

        error_log('[SettingsController] Données reçues: ' . json_encode($data));
        error_log('[SettingsController] Anciennes valeurs: workDuration=' . $settings->getWorkDuration());

        if (isset($data['workDuration'])) {
            $settings->setWorkDuration($data['workDuration']);
            error_log('[SettingsController] Nouvelle workDuration: ' . $data['workDuration']);
        }

        if (isset($data['shortBreakDuration'])) {
            $settings->setShortBreakDuration($data['shortBreakDuration']);
        }

        if (isset($data['longBreakDuration'])) {
            $settings->setLongBreakDuration($data['longBreakDuration']);
        }

        if (isset($data['pomodorosUntilLongBreak'])) {
            $settings->setPomodorosUntilLongBreak($data['pomodorosUntilLongBreak']);
        }

        $this->entityManager->flush();

        error_log('[SettingsController] Après flush: workDuration=' . $settings->getWorkDuration());

        $response = [
            'workDuration' => $settings->getWorkDuration(),
            'shortBreakDuration' => $settings->getShortBreakDuration(),
            'longBreakDuration' => $settings->getLongBreakDuration(),
            'pomodorosUntilLongBreak' => $settings->getPomodorosUntilLongBreak(),
        ];

        error_log('[SettingsController] Réponse envoyée: ' . json_encode($response));

        return $this->json($response);
    }
}

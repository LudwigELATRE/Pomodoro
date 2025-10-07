<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        $client = $clientRegistry->getClient('google');

        try {
            $googleUser = $client->fetchUser();

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->toArray()['name'] ?? $email;
            $avatarUrl = $googleUser->toArray()['picture'] ?? null;

            $user = $this->userRepository->findByGoogleId($googleId);

            if (!$user) {
                $user = $this->userRepository->findByEmail($email);

                if (!$user) {
                    $user = new User();
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setAuthProvider('google');

                    $settings = new UserSettings();
                    $settings->setUser($user);
                    $user->setSettings($settings);

                    $this->entityManager->persist($settings);
                }

                $user->setGoogleId($googleId);
                $user->setAvatarUrl($avatarUrl);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            $token = $this->jwtManager->create($user);

            // Redirect to frontend with token
            $frontendUrl = $_ENV['FRONTEND_URL'] ?? 'http://localhost:5173';

            return $this->redirect($frontendUrl . '/auth/callback?token=' . $token);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Google authentication failed: ' . $e->getMessage()], 500);
        }
    }
}

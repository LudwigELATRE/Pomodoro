<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            return $this->json(['error' => 'Email already exists'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setAuthProvider('local');

        $settings = new UserSettings();
        $settings->setUser($user);
        $user->setSettings($settings);

        $this->entityManager->persist($user);
        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'avatarUrl' => $user->getAvatarUrl(),
                'authProvider' => $user->getAuthProvider(),
            ]
        ], 201);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Missing credentials'], 400);
        }

        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'avatarUrl' => $user->getAvatarUrl(),
                'authProvider' => $user->getAuthProvider(),
            ]
        ]);
    }

    #[Route('/auth/google', methods: ['POST'])]
    public function googleAuth(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token'])) {
            return $this->json(['error' => 'Missing Google token'], 400);
        }

        try {
            $client = new \Google_Client(['client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID']]);
            $payload = $client->verifyIdToken($data['token']);

            if (!$payload) {
                return $this->json(['error' => 'Invalid Google token'], 401);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'] ?? '';
            $avatarUrl = $payload['picture'] ?? null;

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

            return $this->json([
                'token' => $token,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'avatarUrl' => $user->getAvatarUrl(),
                    'authProvider' => $user->getAuthProvider(),
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Google authentication failed: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'avatarUrl' => $user->getAvatarUrl(),
            'authProvider' => $user->getAuthProvider(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JWTService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/v1")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, JWTService $jwtService): Response
    {
        $body = json_decode($request->getContent(), true);
        // проверяем данные пользователя
        if (empty($body['email']) || empty($body['password'])) {
            throw new BadRequestException('Некорректные данные пользователя!');
        }
        $email = $body['email'];
        $password = $body['password'];
        // находим пользователя по почте
        $user = $userRepository->getUserByEmail($email);
        if ($user === null) {
            throw new NotFoundHttpException('Неверный логин или пароль!');
        }
        // проверяем пароль пользователя
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            throw new BadRequestException('Неверный логин или пароль!');
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'token' => $jwtService->createToken($user->getId())
        ]);
    }

    /**
     * @Route("/validate", name="validate", methods={"POST"})
     */
    public function validate(Request $request, JWTService $jwtService)
    {
        $token = $request->headers->get('Authorization');
        // проверка заголовка
        if ($token === null) {
            throw new BadRequestException('Заголовок Authorization не содержит JWT Bearer токен');
        }
        // проверка токена
        $tokenParts = explode(' ', $token);
        if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
            throw new BadRequestException('Заголовок Authorization не содержит JWT Bearer токен');
        }
        // валидация токена
        $jwtService->validateToken($tokenParts[1]);

        return $this->json(['message' => 'Токен валиден']);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $body = json_decode($request->getContent(), true);
        // проверяем данные нового пользователя
        if (empty($body['email']) || empty($body['password']) || empty($body['confirm_password'])) {
            throw new BadRequestException('Некорректные данные пользователя!');
        }
        // проверяем формат почты
        $email = $body['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException('Некорректный формат почты!');
        }
        // проверяем длину пароля
        $password = $body['password'];
        $confirmPassword = $body['confirm_password'];
        if (strlen($password) < 6) {
            throw new BadRequestException('Длина пароля должна быть не менее 6 символов!');
        }
        // проверяем совпадение паролей
        if ($password !== $confirmPassword) {
            throw new BadRequestException('Пароли должны совпадать!');
        }
        // создаём пользователя
        try {
            $user = new User();
            $user->setEmail($body['email']);
            $user->setPassword($encoder->encodePassword($user, $password));
            $manager->persist($user);
            $manager->flush();
        }
        catch (UniqueConstraintViolationException $e)
        {
            throw new BadRequestException('Пользователь с данной почтой уже зарегистрирован!');
        }

        return $this->json(['message' => 'Пользователь успешно зарегистрирован!'], 201);
    }
}

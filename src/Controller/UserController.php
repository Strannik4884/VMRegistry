<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(): Response
    {
        return $this->json([
            'id' => 1,
            'email' => 'test@example.com',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c'
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(): Response
    {
        return $this->json(['message' => 'Successful']);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(): Response
    {
        return $this->json(['message' => 'Successful'], 201);
    }

    /**
     * @Route("/password/change", name="change_password", methods={"POST"})
     */
    public function changePassword(): Response
    {
        return $this->json(['message' => 'Successful']);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class VMUserController extends AbstractController
{
    /**
     * @Route("/vms/{id<\d+>}/users", name="get_vms_users", methods={"GET"})
     */
    public function getVMUsers(): Response
    {
        return $this->json([
            [
                'id' => 1,
                'login' => 'server',
                'password' => '12345',
                'description' => 'Основной серверный аккаунт'
            ],
            [
                'id' => 2,
                'login' => 'root',
                'password' => '12345',
                'description' => null
            ]
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}/users", name="add_new_vm_user", methods={"POST"})
     */
    public function addNewVMUser(): Response
    {
        return $this->json([
            'id' => 3,
            'login' => 'docker',
            'password' => '123IJoifhoiwe45',
            'description' => 'Пользователь docker'
        ], 201);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{user_id<\d+>}", name="get_vm_user_by_id", methods={"GET"})
     */
    public function getVMUserById(): Response
    {
        return $this->json([
            'id' => 1,
            'login' => 'server',
            'password' => '12345',
            'description' => 'Основной серверный аккаунт'
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{user_id<\d+>}", name="update_vm_user_by_id", methods={"PATCH"})
     */
    public function updateVMUserById(): Response
    {
        return $this->json([
            'id' => 1,
            'login' => 'server_patched',
            'password' => '12345',
            'description' => 'Основной серверный аккаунт'
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{user_id<\d+>}", name="delete_vm_user_by_id", methods={"DELETE"})
     */
    public function deleteVMUserById(): Response
    {
        return $this->json([], 204);
    }
}

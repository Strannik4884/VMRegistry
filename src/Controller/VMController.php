<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class VMController extends AbstractController
{
    /**
     * @Route("/vms/types", name="vms_types", methods={"GET"})
     */
    public function getVMsTypes(): Response
    {
        return $this->json([
            'types' => [
                [
                    'id' => 1,
                    'name' => 'Ubuntu',
                    'image' => 'ubuntu.png'
                ],
                [
                    'id' => 2,
                    'name' => 'Windows',
                    'image' => 'windows.png'
                ],
                [
                    'id' => 3,
                    'name' => 'MacOS',
                    'image' => 'macos.png'
                ]
            ]
        ]);
    }

    /**
     * @Route("/vms", name="get_vms", methods={"GET"})
     */
    public function getVMs(): Response
    {
        return $this->json([
            'vms' => [
                [
                    'id' => 1,
                    'name' => 'RSL Dev',
                    'type' => [
                        'id' => 1,
                        'name' => 'Ubuntu',
                        'image' => 'ubuntu.png'
                    ],
                    'address' => 'rsl-dev.ru',
                    'ssh_port' => 22,
                    'description' => 'Виртуальная машина тестового контура РГБ'
                ],
                [
                    'id' => 2,
                    'name' => 'Ozone',
                    'type' => [
                        'id' => 1,
                        'name' => 'Ubuntu',
                        'image' => 'ubuntu.png'
                    ],
                    'address' => '23.43.232.54',
                    'ssh_port' => 3333,
                    'description' => null
                ]
            ]
        ]);
    }

    /**
     * @Route("/vms", name="add_vm", methods={"POST"})
     */
    public function addVM(): Response
    {
        return $this->json([
            'id' => 3,
            'name' => 'Win Test',
            'type' => [
                'id' => 2,
                'name' => 'Windows',
                'image' => 'windows.png'
            ],
            'address' => '22.33.44.124',
            'ssh_port' => 22,
            'description' => 'Виртуальная машина на Windows Server 2010'
        ], 201);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="get_vm_by_id", methods={"GET"})
     */
    public function getVMById(): Response
    {
        return $this->json([
            'id' => 1,
            'name' => 'RSL Dev',
            'type' => [
                'id' => 1,
                'name' => 'Ubuntu',
                'image' => 'ubuntu.png'
            ],
            'address' => 'rsl-dev.ru',
            'ssh_port' => 22,
            'description' => 'Виртуальная машина тестового контура РГБ',
            'vm_users' => [
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
            ]
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="update_vm_by_id", methods={"PATCH"})
     */
    public function updateVMById(): Response
    {
        return $this->json([
            'id' => 1,
            'name' => 'RSL Dev patched',
            'type' => [
                'id' => 1,
                'name' => 'Ubuntu',
                'image' => 'ubuntu.png'
            ],
            'address' => 'rsl-dev.ru',
            'ssh_port' => 22,
            'description' => 'Виртуальная машина тестового контура РГБ'
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="delete_vm_by_id", methods={"DELETE"})
     */
    public function deleteVMById(): Response
    {
        return $this->json([], 204);
    }
}

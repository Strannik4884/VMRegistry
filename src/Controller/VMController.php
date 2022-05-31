<?php

namespace App\Controller;

use App\Entity\VM;
use App\Repository\UserRepository;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class VMController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/vms", name="get_vms", methods={"GET"})
     */
    public function getVMs(Request $request, UserRepository $userRepository, JWTService $jwtService): Response
    {
        // проверяем пользователя по JWT
        $requestToken = $request->headers->get('Authorization');
        $token = $jwtService->parseToken(explode(' ', $requestToken)[1]);
        $userId = (int) $token->claims()->get('user_id');
        $user = $userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден!');
        }

        $vms = $user->getVms();
        $response = [];
        foreach ($vms as $vm) {
            $response[] = [
                'id' => $vm->getId(),
                'name' => $vm->getName(),
                'address' => $vm->getAddress(),
                'ssh_port' => $vm->getsshPort(),
                'description' => $vm->getDescription()
            ];
        }

        return $this->json(['vms' => $response]);
    }

    /**
     * @Route("/vms", name="add_vm", methods={"POST"})
     */
    public function addVM(Request $request, JWTService $jwtService, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        // проверяем пользователя по JWT
        $requestToken = $request->headers->get('Authorization');
        $token = $jwtService->parseToken(explode(' ', $requestToken)[1]);
        $userId = (int) $token->claims()->get('user_id');
        $user = $userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден!');
        }
        // парсим тело ответа
        $body = json_decode($request->getContent(), true);
        // проверяем данные новой VM
        if (empty($body['name']) || empty($body['address']) || empty($body['ssh_port'])) {
            throw new BadRequestException('Некорректные данные VM!');
        }
        $name = $body['name'];
        $address = $body['address'];
        $sshPort = $body['ssh_port'];
        $description = null;
        if(!empty($body['description'])) {
            $description = $body['description'];
        }
        // валидация данных
        if (strlen($address) < 4) {
            throw new BadRequestException('Длина адреса должна быть не менее 4 символов!');
        }
        $sshPort = (int) $sshPort;
        if ($sshPort > 65536 || $sshPort < 1) {
            throw new BadRequestException('Порт SSH должен быть корректным числом!');
        }
        // создание VM
        $vm = new VM();
        $vm->setName($name);
        $vm->setAddress($address);
        $vm->setsshPort($sshPort);
        $vm->setDescription($description);
        $vm->setOwner($user);
        $manager->persist($vm);
        $manager->flush();

        return $this->json([
            'id' => $vm->getId(),
            'name' => $vm->getName(),
            'address' => $vm->getAddress(),
            'ssh_port' => $vm->getsshPort(),
            'description' => $vm->getDescription()
        ], 201);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="get_vm_by_id", methods={"GET"})
     */
    public function getVMById(int $id, Request $request, UserRepository $userRepository, JWTService $jwtService): Response
    {
        // проверяем пользователя по JWT
        $requestToken = $request->headers->get('Authorization');
        $token = $jwtService->parseToken(explode(' ', $requestToken)[1]);
        $userId = (int) $token->claims()->get('user_id');
        $user = $userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден!');
        }

        $vms = $user->getVms();
        $currentVm = null;

        foreach ($vms as $vm) {
            if ($vm->getId() === $id) {
                $currentVm = $vm;
                break;
            }
        }
        if ($currentVm === null) {
            throw new NotFoundHttpException('VM не найдена!');
        }

        $vmUsers = $currentVm->getVmUsers();
        $responseUsers = [];
        foreach ($vmUsers as $vmUser) {
            $responseUsers[] = [
                'id' => $vmUser->getId(),
                'login' => $vmUser->getLogin(),
                'password' => $vmUser->getPassword(),
                'description' => $vmUser->getDescription()
            ];
        }

        return $this->json([
            'id' => $currentVm->getId(),
            'name' => $currentVm->getName(),
            'address' => $currentVm->getAddress(),
            'ssh_port' => $currentVm->getsshPort(),
            'description' => $currentVm->getDescription(),
            'users' => $responseUsers
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="update_vm_by_id", methods={"PATCH"})
     */
    public function updateVMById(int $id, Request $request, JWTService $jwtService, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        // проверяем пользователя по JWT
        $requestToken = $request->headers->get('Authorization');
        $token = $jwtService->parseToken(explode(' ', $requestToken)[1]);
        $userId = (int) $token->claims()->get('user_id');
        $user = $userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден!');
        }
        // парсим тело ответа
        $body = json_decode($request->getContent(), true);
        // проверяем данные новой VM
        if (empty($body['name']) || empty($body['address']) || empty($body['ssh_port'])) {
            throw new BadRequestException('Некорректные данные VM!');
        }
        $name = $body['name'];
        $address = $body['address'];
        $sshPort = $body['ssh_port'];
        $description = null;
        if(!empty($body['description'])) {
            $description = $body['description'];
        }

        $vms = $user->getVms();
        $currentVm = null;
        foreach ($vms as $vm) {
            if ($vm->getId() === $id) {
                $currentVm = $vm;
                break;
            }
        }
        if ($currentVm === null) {
            throw new NotFoundHttpException('VM не найдена!');
        }

        // валидация данных
        if (strlen($address) < 4) {
            throw new BadRequestException('Длина адреса должна быть не менее 4 символов!');
        }
        $sshPort = (int) $sshPort;
        if ($sshPort > 65536 || $sshPort < 1) {
            throw new BadRequestException('Порт SSH должен быть корректным числом!');
        }
        // обновление данных VM
        $currentVm->setName($name);
        $currentVm->setAddress($address);
        $currentVm->setsshPort($sshPort);
        $currentVm->setDescription($description);
        $manager->persist($currentVm);
        $manager->flush();

        return $this->json([
            'id' => $currentVm->getId(),
            'name' => $currentVm->getName(),
            'address' => $currentVm->getAddress(),
            'ssh_port' => $currentVm->getsshPort(),
            'description' => $currentVm->getDescription()
        ], 200);
    }

    /**
     * @Route("/vms/{id<\d+>}", name="delete_vm_by_id", methods={"DELETE"})
     */
    public function deleteVMById(int $id, Request $request, UserRepository $userRepository, JWTService $jwtService, EntityManagerInterface $manager): Response
    {
        // проверяем пользователя по JWT
        $requestToken = $request->headers->get('Authorization');
        $token = $jwtService->parseToken(explode(' ', $requestToken)[1]);
        $userId = (int) $token->claims()->get('user_id');
        $user = $userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден!');
        }

        $vms = $user->getVms();
        $currentVm = null;

        foreach ($vms as $vm) {
            if ($vm->getId() === $id) {
                $currentVm = $vm;
                break;
            }
        }
        if ($currentVm === null) {
            throw new NotFoundHttpException('VM не найдена!');
        }

        $manager->remove($currentVm);
        $manager->flush();

        return $this->json([], 204);
    }
}

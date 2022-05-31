<?php

namespace App\Controller;

use App\Entity\VMUser;
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
class VMUserController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/vms/{id<\d+>}/users", name="get_vms_users", methods={"GET"})
     */
    public function getVMUsers(int $id, Request $request, UserRepository $userRepository, JWTService $jwtService): Response
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
        $response = [];
        foreach ($vmUsers as $vmUser) {
            $response[] = [
                'id' => $vmUser->getId(),
                'login' => $vmUser->getLogin(),
                'password' => $vmUser->getPassword(),
                'description' => $vmUser->getDescription()
            ];
        }

        return $this->json(['vm_users' => $response]);
    }

    /**
     * @Route("/vms/{id<\d+>}/users", name="add_new_vm_user", methods={"POST"})
     */
    public function addNewVMUser(int $id, Request $request, UserRepository $userRepository, JWTService $jwtService, EntityManagerInterface $manager): Response
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

        // парсим тело ответа
        $body = json_decode($request->getContent(), true);
        // проверяем данные нового пользователя VM
        if (empty($body['login']) || empty($body['password'])) {
            throw new BadRequestException('Некорректные данные пользователя VM!');
        }
        $login = $body['login'];
        $password = $body['password'];
        $description = null;
        if(!empty($body['description'])) {
            $description = $body['description'];
        }
        // создание пользователя VM
        $vmUser = new VMUser();
        $vmUser->setLogin($login);
        $vmUser->setPassword($password);
        $vmUser->setDescription($description);
        $vmUser->setVm($currentVm);
        $manager->persist($vmUser);
        $manager->flush();

        return $this->json([
            'id' => $vmUser->getId(),
            'login' => $vmUser->getLogin(),
            'password' => $vmUser->getPassword(),
            'description' => $vmUser->getDescription()
        ], 201);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{vmuserId<\d+>}", name="get_vm_user_by_id", methods={"GET"})
     */
    public function getVMUserById(int $id, int $vmuserId, Request $request, UserRepository $userRepository, JWTService $jwtService): Response
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
        $currentUser = null;
        foreach ($vmUsers as $vmUser) {
            if ($vmUser->getId() === $vmuserId) {
                $currentUser = $vmUser;
                break;
            }
        }
        if ($currentUser === null) {
            throw new NotFoundHttpException('Пользователь VM не найден!');
        }

        return $this->json([
            'id' => $currentUser->getId(),
            'login' => $currentUser->getLogin(),
            'password' => $currentUser->getPassword(),
            'description' => $currentUser->getDescription()
        ]);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{vmuserId<\d+>}", name="update_vm_user_by_id", methods={"PATCH"})
     */
    public function updateVMUserById(int $id, int $vmuserId, Request $request, UserRepository $userRepository, JWTService $jwtService, EntityManagerInterface $manager): Response
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

        // парсим тело ответа
        $body = json_decode($request->getContent(), true);
        // проверяем данные нового пользователя VM
        if (empty($body['login']) || empty($body['password'])) {
            throw new BadRequestException('Некорректные данные пользователя VM!');
        }
        $login = $body['login'];
        $password = $body['password'];
        $description = null;
        if(!empty($body['description'])) {
            $description = $body['description'];
        }

        $vmUsers = $currentVm->getVmUsers();
        $currentUser = null;
        foreach ($vmUsers as $vmUser) {
            if ($vmUser->getId() === $vmuserId) {
                $currentUser = $vmUser;
                break;
            }
        }
        if ($currentUser === null) {
            throw new NotFoundHttpException('Пользователь VM не найден!');
        }

        // обновление пользователя VM
        $currentUser->setLogin($login);
        $currentUser->setPassword($password);
        $currentUser->setDescription($description);
        $manager->persist($currentUser);
        $manager->flush();

        return $this->json([
            'id' => $currentUser->getId(),
            'login' => $currentUser->getLogin(),
            'password' => $currentUser->getPassword(),
            'description' => $currentUser->getDescription()
        ], 200);
    }

    /**
     * @Route("/vms/{id<\d+>}/users/{vmuserId<\d+>}", name="delete_vm_user_by_id", methods={"DELETE"})
     */
    public function deleteVMUserById(int $id, int $vmuserId, Request $request, UserRepository $userRepository, JWTService $jwtService, EntityManagerInterface $manager): Response
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
        $currentUser = null;
        foreach ($vmUsers as $vmUser) {
            if ($vmUser->getId() === $vmuserId) {
                $currentUser = $vmUser;
                break;
            }
        }
        if ($currentUser === null) {
            throw new NotFoundHttpException('Пользователь VM не найден!');
        }

        $manager->remove($currentUser);
        $manager->flush();

        return $this->json([], 204);
    }
}

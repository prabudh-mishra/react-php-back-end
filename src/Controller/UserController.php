<?php


namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/api/user", name="get_user_list", methods={"GET"})
     */
    public function fetchUsers(Request $request): Response
    {
        $userList = array();
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
            ];
        }

        return new JsonResponse($userList, Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/{id}", name="get_user_details", methods={"GET"})
     */
    public function fetchUserDetails(Request $request, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse('User not available', Response::HTTP_NOT_FOUND);
        }

        $userDetails = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        return new JsonResponse($userDetails, Response::HTTP_OK);
    }

    /**
     * @Route("/api/user", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request): Response
    {
        $user = new User();

        $user
            ->setFirstName($request->get('firstName'))
            ->setLastName($request->get('lastName'))
            ->setEmail($request->get('email'))
            ->setPassword($this->passwordHasher->hashPassword($user, $request->get('password')));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userDetails = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        return new JsonResponse($userDetails, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/user/{id}", name="edit_user", methods={"PUT", "PATCH"})
     */
    public function editUser(Request $request, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse('User not available', Response::HTTP_NOT_FOUND);
        }

        $user
            ->setFirstName($request->get('firstName'))
            ->setLastName($request->get('lastName'))
            ->setEmail($request->get('email'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userDetails = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        return new JsonResponse($userDetails, Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(Request $request, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse('User not available', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse('User has been deleted successfully', Response::HTTP_OK);
    }
}

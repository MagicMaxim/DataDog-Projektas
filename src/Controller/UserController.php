<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("ROLE_ADMIN")
 */

class UserController extends AbstractController
{
    /**
     * @Route("/users/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository) : Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll() ]);
    }

    /**
     * @Route("/users/{id}", name="user_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}

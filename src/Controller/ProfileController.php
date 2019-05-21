<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfileController extends BaseController
{
    /**
     * @Route("/profile", name="app_profile")
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new RedirectResponse($urlGenerator->generate('app_profile'));
        }


        return $this->render('profile/index.html.twig');
    }


}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordChangeType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PasswordChangeController extends Controller
{
    /**
     * @Route("/changepsw", name="app_passwordchange")
     */
    public function PasswordChange(Request $request, UserPasswordEncoderInterface $encoder, AuthorizationCheckerInterface $authChecker)
    {
        $newPassword = new User();
        $user = $this->getUser();
        $error = "";
        $form = $this->createForm(PasswordChangeType::class, $newPassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            if($encoder->isPasswordValid($user, $newPassword->getPassword()))
            {
                //The old password which user used is correct.
                //So we can change the password in user object
                $user->setPassword($encoder->encodePassword($user, $newPassword->getNewPassword()));
                $em->persist($user);
                $em->flush();
                return $this->render('profile/PasswordChange.html.twig', array(
                    'success' => "Password changed succesfully.",
                    'form'=>$form->createView(),
                ));
            }else{
                $error = "Current password is wrong.";
            }
        }
        return $this->render('profile/PasswordChange.html.twig', array(
            'error' => $error,
            'form'=>$form->createView(),
        ));
    }
    public function show()
    {
        return $this->render('profile/PasswordChange.html.twig', [
        ]);
    }
}
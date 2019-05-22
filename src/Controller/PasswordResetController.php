<?php

namespace App\Controller;

use App\Entity\PasswordChange;
use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\ResetPasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetController extends AbstractController
{

    /**
     * @Route("/reset", name="app_reset")
     */
    public function reset(Request $request, \Swift_Mailer $mailer): Response
    {
        $error="";
        $user = new PasswordChange();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($user)
            {
                $passwordResetToken = base64_encode(random_bytes(20));
                $passwordResetToken = str_replace("/","",$passwordResetToken); // because / will make errors with routes
                $user->setPasswordResetToken($passwordResetToken);
                $this->getDoctrine()->getManager()->flush();
                $message = (new \Swift_Message('Password reset'))
                    ->setFrom('DMTprojektas@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'reset/passwordreset.html.twig',
                            array(
                                'token' => $passwordResetToken,
                                'username' => $user->getUsername()
                            )
                        ),
                        'text/html'
                    )
                ;
                $mailer->send($message);
                return $this->render('reset/reset.html.twig', [
                'form'=>$form->createView(),
                'action' => "reset",
                'success' => "Password change link has been sent to your email."
                ]);
            }
            else {
                $error="Error, user not found.";
            }
        }
        
        return $this->render('reset/reset.html.twig', [
            'form'=>$form->createView(),
            'action' => "reset",
            'error' => $error
        ]);
    }

    /**
     * @Route("/reset/{token}", name="app_reset")
     */
    public function setNewPassword(Request $request, $token, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);
        if($user == null)
        {
            return $this->redirectToRoute('index');
        }
        $newPassword = new User();
        $form = $this->createForm(NewPasswordType::class, $newPassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $password = $encoder->encodePassword($user, $newPassword->getPassword());
            $user->setPassword($password);
            //"NULL" because it has to be a string; when doing a password reset, we must then
            // check if token is not "NULL"
            $user->setPasswordResetToken(NULL);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset/reset.html.twig', [
            'form'=>$form->createView(),
            'action' => "set"
        ]);
    }
}
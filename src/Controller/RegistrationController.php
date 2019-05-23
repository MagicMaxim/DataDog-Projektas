<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $passwordResetToken = base64_encode(random_bytes(20));
            $passwordResetToken = str_replace("/","",$passwordResetToken); // because / will make errors with routes
            $user->setPasswordResetToken($passwordResetToken);
            $entityManager->persist($user);
            $entityManager->flush();
            $message = (new \Swift_Message('Succesfully registered'))
                    ->setFrom('DMTprojektas@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'registration/confirmationMail.html.twig',
                            array(
                                'token' => $passwordResetToken,
                                'username' => $user->getUsername()
                            )
                        ),
                        'text/html'
                    )
                ;
            $mailer->send($message);
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/confirmation/{token}", name="app_confirmation")
     */
    public function Confirmation(Request $request, $token, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);
        if($user != null)
        {
            $em = $this->getDoctrine()->getManager();
            $user->setPasswordResetToken(NULL);
            $user->setRoles(array('ROLE_CONFIRMED'));
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        return $this->redirectToRoute('index');
    }
}

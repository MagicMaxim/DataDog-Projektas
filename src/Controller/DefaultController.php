<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    public function index()
    {
        return $this->render('home.html.twig', [
            'isLoged' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isConfirmed' => $this->isGranted('ROLE_CONFIRMED')
        ]);
    }
}
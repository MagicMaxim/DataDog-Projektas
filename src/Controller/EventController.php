<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event")
     */
    public function index()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $event = new Event();
        $event->setName('Asd');
        $event->setPrice(1999);
        $event->setLocation('Lietuva');

        // tell Doctrine you want to (eventually) save the e$event (no queries yet)
        $entityManager->persist($event);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new $event with id '.$event->getId());
    }
        /**
     * @Route("/event/{id}", name="event_show")
     */
    public function show($id)
    {
        $event = $this->getDoctrine()
            ->getRepository(event::class)
            ->find($id);

        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$id
            );
        }

        return new Response('Check out this great event: '.$event->getName());

        // or render a template
        // in the template, print things with {{ event.name }}
        // return $this->render('event/show.html.twig', ['event' => $event]);
    }
}

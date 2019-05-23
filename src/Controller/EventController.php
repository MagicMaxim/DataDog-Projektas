<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Category;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    private $category;
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */

    public function index(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $user = $this->getUser();      
        $eventQuery = $eventRepository->findAllWithCategories();
        $pagination = $paginator->paginate(
            $eventQuery,
            $request->query->getInt('page', 1)/*page number*/,
            25/*limit per page*/
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
            'isLoged' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isConfirmed' => $this->isGranted('ROLE_CONFIRMED'),
            'subscribed' => $user === null ? '' : $user->getAllUserEvents()
        ]);

        
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */

    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_index', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * @Route("/attend/{id}", name="event_subscribe", methods={"GET"})
     */
    public function attend(Request $request, Event $event): Response
    {
        $user = $this->getUser();
        $user->addEventToUser($event);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('event/attend.html.twig');
    }

    /**
     * @Route("/noattend/{id}", name="event_unsubscribe", methods={"GET"})
     */
    public function notAttend(Request $request, Event $event): Response
    {
        $user = $this->getUser();
        $user->removeEventfromUser($event);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('event/notAttend.twig');
    }
     /**
     * @Route("/", name="filter")
     */
    public function filter(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Event')->createQueryBuilder('e');

        if ($request->query->getAlnum('filter')) {
            $queryBuilder->where('e.title LIKE :title')
                ->setParameter('title', '%' . $request->query->getAlnum('filter') . '%');
        }
        if ($request->query->getAlnum('filter2')) {
            $queryBuilder->where('e.category LIKE :category')
                ->setParameter('category', '%' . $request->query->getAlnum('filter2') . '%');
        }
        if ($request->query->getAlnum('filter3')) {
            $queryBuilder->where('e.date LIKE :date')
                ->setParameter('date', '%' . $request->query->getAlnum('filter3') . '%');
        }
        if ($request->query->getAlnum('filter4')) {
            $queryBuilder->where('e.price LIKE :price')
                ->setParameter('price', '%' . $request->query->getAlnum('filter4') . '%');
        }
    }

}   

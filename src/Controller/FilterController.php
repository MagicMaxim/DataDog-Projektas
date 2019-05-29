<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EventFilterType;
use App\Repository\DateTime;

class FilterController extends AbstractController
{
    /**
     * @Route("/filter", name="filter")
     */
    public function filter(EventRepository $eventRepository, Request $request, PaginatorInterface $paginator)
    {
        $form = $this->createForm(EventFilterType::class);
        $form->handleRequest($request);
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        
        if($form->isSubmitted() && $form->isValid()){
            $params = $request->request->all()['event_filter'];
            $queryBuilder = $eventRepository->getEventsByCriteria($params['title'], $params['description'], $params['price'], $params['location']);
        } else {
            $queryBuilder = $eventRepository->getWithSearchQueryBuilder();
        }

        $user = $this->getUser();      
        $eventQuery = $eventRepository->findAllWithCategories();
        $pagination = $paginator->paginate(
            $eventQuery,
            $request->query->getInt('page', 1)/*page number*/,
            25/*limit per page*/
        );

        return $this->render('event/filter.html.twig', [
            'events' => $pagination,
            'form' => $form->createView(),
        ]);
    }
}

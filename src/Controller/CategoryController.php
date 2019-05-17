<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class CategoryController extends AbstractController
{
    /**
     * @Route("/category/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator) : Response
    {
        $user = $this->getUser();
        $categoryQuery = $categoryRepository->findAll();

        $pagination = $paginator->paginate(
            $categoryQuery,
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );


        return $this->render('category/index.html.twig', [
            'categories' => $pagination,
            'isLoged' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'subscribed' => $user === null ? '' : $user->getAllUserCategories()
        ]);
    }

    /**
     * @Route("/category/new", name="category_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */

    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            '$category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("category/{id}/edit", name="category_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * @Route("/subscribe/{id}", name="category_subscribe", methods={"GET"})
     */
    public function subscribe(Request $request, Category $category): Response
    {
        $user = $this->getUser();
        $user->addCategoryToUser($category);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('category/subscribe.html.twig');
    }

    /**
     * @Route("/unsubscribe/{id}", name="category_unsubscribe", methods={"GET"})
     */
    public function unsubscribe(Request $request, Category $category): Response
    {
        $user = $this->getUser();
        $user->removeCategoryfromUser($category);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('category/unsubscribe.twig');
    }
}

<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_list")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CategoryRepository $categoryRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(CategoryRepository $categoryRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin() && !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/create", name="create_category")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin() && !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('category_list');
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function edit(Request $request, Category $category)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin() && !$user->isEditor()) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_list', [
                'id' => $category->getId()
            ]);
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/category/delete{id}", name="category_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Category $category)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('category_list');
    }
}
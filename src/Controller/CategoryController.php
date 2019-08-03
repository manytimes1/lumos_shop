<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
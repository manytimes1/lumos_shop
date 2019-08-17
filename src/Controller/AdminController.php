<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/user/staff", name="staff_list")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function staff(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $users = $userRepository->findAll();

        return $this->render('admin/staff/list_all.html.twig', [
            'users' => $users
        ]);
    }
}
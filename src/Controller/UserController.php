<?php


namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profile()
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $currentUser = $userRepository->find($this->getUser());

        return $this->render('user/profile.html.twig', [
            'user' => $currentUser
        ]);
    }
}
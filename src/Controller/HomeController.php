<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $user = $this->getUser();

        if ($user) {
            $profile = $user->getProfile();

            return $this->render('home/index.html.twig', [
                'user' => $user,
                'profile' => $profile
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}

<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="list_all_customers")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAll(UserRepository $userRepository)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $customers = $userRepository->findAllCustomersByRole();

        return $this->render('customer/list.html.twig', [
            'customers' => $customers
        ]);
    }
}
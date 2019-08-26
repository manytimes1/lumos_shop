<?php


namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $form = $this->createForm(UserType::class);

        return $this->render('customer/list.html.twig', [
            'customers' => $customers,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/add", name="add_new_customer")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RoleRepository $roleRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addNew(Request $request,
                           UserPasswordEncoderInterface $passwordEncoder,
                           RoleRepository $roleRepository)
    {
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $role = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $user->addRole($role);

        $profile = new Profile();
        $profile->setFirstName($user->getProfile()->getFirstName());
        $profile->setLastName($user->getProfile()->getLastName());
        $profile->setPhone($user->getProfile()->getPhone());
        $profile->setLocation($user->getProfile()->getLocation());
        $profile->setCash(1000);
        $profile->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($profile);

        $user->setProfile($profile);
        $user->setStatus(1);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Customer added successfully.');

        return $this->redirectToRoute('list_all_customers');
    }

    /**
     * @Route("/customer/{id}", name="customer_detail")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param UserRepository $userRepository
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(UserRepository $userRepository, User $user)
    {
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $customer = $userRepository->find($user);
        $form = $this->createForm(UserType::class, $user);

        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/{id}/delete", name="customer_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(User $user)
    {
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Customer deleted successfully.');

        return $this->redirectToRoute('list_all_customers');
    }

    /**
     * @Route("/customer/{id}/change-status", name="change_user_status")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param User $user
     */
    public function changeStatus(User $user)
    {
        if ($user->isEnabled()) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('customer_detail', [
            'id' => $user->getId()
        ]);
    }
}
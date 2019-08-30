<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StaffController extends AbstractController
{
    /**
     * @Route("/user/staff", name="staff_list")
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

        $employees = $userRepository->findAllCustomersByRole('ROLE_EDITOR');

        $form = $this->createForm(UserType::class);

        return $this->render('staff/list.html.twig', [
            'employees' => $employees,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/staff/add-new", name="add_new_staff")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add(Request $request,
                        UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $currentUser */
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

        $role = $form->get('roles')->getData();
        $user->addRole($role);

        $profile = new Profile();
        $profile->setFirstName($user->getProfile()->getFirstName());
        $profile->setLastName($user->getProfile()->getLastName());
        $profile->setPhone($user->getProfile()->getPhone());
        $profile->setLocation($user->getProfile()->getLocation());
        $profile->setCity($user->getProfile()->getCity());
        $profile->setState($user->getProfile()->getState());
        $profile->setZipCode($user->getProfile()->getZipCode());
        $profile->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($profile);

        $user->setProfile($profile);
        $user->setEnabled(1);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Staff added successfully.');

        return $this->redirectToRoute('staff_list');
    }

    /**
     * @Route("/user/staff/{id}", name="staff_detail")
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

        $employee = $userRepository->find($user);
        $form = $this->createForm(UserType::class, $user);

        return $this->render('staff/show.html.twig', [
            'employee' => $employee,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/staff/{id}/update", name="update_staff_information")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Staff updated successfully.');

        return $this->redirectToRoute('staff_detail', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route("/user/staff/{id}/delete", name="delete_staff")
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

        $this->addFlash('success', 'Staff deleted successfully.');

        return $this->redirectToRoute('staff_list');
    }
}
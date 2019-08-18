<?php


namespace App\Controller;


use App\Entity\Profile;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/staff", name="staff_list")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function staff(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        /** @var User $user */
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $users = $userRepository->findAll();
        $roles = $roleRepository->findBy(['name' => 'ROLE_EDITOR']);

        return $this->render('admin/staff/list_all.html.twig', [
            'users' => $users,
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/staff/add", name="add_new_staff")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addNewStaff(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$this->getUser()->isAdmin()) {
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
        $profile->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($profile);

        $user->setProfile($profile);
        $user->setStatus(1);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Staff member added successfully.');

        return $this->redirectToRoute('staff_list');
    }
}
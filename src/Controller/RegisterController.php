<?php


namespace App\Controller;


use App\Entity\Profile;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @Route("/register", name="register")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);

            $em = $this->getDoctrine()->getManager();

            if (null == $roleRepository->findAll()) {
                $role = new Role();
                $role->setName('ROLE_USER');

                $em->persist($role);
                $em->flush();
            }

            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

            $profile = new Profile();
            $profile->setFirstName($user->getProfile()->getFirstName());
            $profile->setLastName($user->getProfile()->getLastName());
            $profile->setCash(1000);
            $profile->setUser($user);

            $em->persist($profile);

            $user->setProfile($profile);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User registered successfully.');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
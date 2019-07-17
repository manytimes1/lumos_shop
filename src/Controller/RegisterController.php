<?php


namespace App\Controller;


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
     * @Route("/lumos_shop/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
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

            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $em = $this->getDoctrine()->getManager();

            if (null == $userRepository->findAll()) {
                $adminRole = new Role();
                $userRole = new Role();
                $editorRole = new Role();

                $adminRole->setName('ROLE_ADMIN');
                $userRole->setName('ROLE_USER');
                $editorRole->setName('ROLE_EDITOR');

                $em->persist($adminRole);
                $em->persist($userRole);
                $em->persist($editorRole);

                $user->addRole($adminRole);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('login');
            }

            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

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
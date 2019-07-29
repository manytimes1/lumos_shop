<?php


namespace App\Controller;

use App\Form\ProfileType;
use App\Form\Type\ChangePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", methods={"GET", "POST"}, name="profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Profile updated successfully.');

            return $this->redirectToRoute('profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'profile' => $profile,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/change-password", name="user_change_password")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword(
                $user,
                $form->get('newPassword')->getData())
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('user/change_password.html.twig', [
            'user' => $user,
            'profile' => $profile,
            'form' => $form->createView()
        ]);
    }
}
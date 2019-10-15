<?php

namespace App\Controller\Configuration;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    /**
     * @var RoleRepository
     */
    private $repo;

    public function __construct(RoleRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/configuration/role", name="role_configuration")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            $this->addFlash('danger', "You don't have permission to perform this action.");

            return $this->redirectToRoute('index');
        }

        $roles = $this->repo->findAll();

        return $this->render('configuration/role/index.html.twig', [
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/configuration/role/crreate", name="create_new_role")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            $this->addFlash('danger', "You don't have permission to perform this action.");

            return $this->redirectToRoute('index');
        }

        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $em->flush();

        $this->addFlash('success', 'Role added.');

        return $this->redirectToRoute('role_configuration');
    }

    /**
     * @Route("/configuration/role/delete/{id}", name="delete_role")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Role $role
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Role $role)
    {
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            $this->addFlash('danger', "You don't have permission to perform this action.");

            return $this->redirectToRoute('index');
        }

        $roles = $this->repo->findBy([
            'name' => [
                'ROLE_ADMIN',
                'ROLE_EDITOR',
                'ROLE_USER'
            ]
        ]);

        if (in_array($role, $roles)) {
            $this->addFlash('danger', 'Default role cannot be deleted.');

            return  $this->redirectToRoute('role_configuration');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($role);
        $em->flush();

        $this->addFlash('success', 'Role deleted');

        return $this->redirectToRoute('role_configuration');
    }
}
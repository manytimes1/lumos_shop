<?php

namespace App\Controller\Configuration;

use App\Entity\ConfigurationInterface;
use App\Form\Type\ModifyConfigurationType;
use App\Repository\ConfigurationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigurationController extends AbstractController
{
    protected $repo;

    public function __construct(ConfigurationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/configuration", name="basic_configuration")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $user = $this->getUser();

        if (!$user->isAdmin()) {
            $this->addFlash('danger', "You don't have permission to do this action.");

            return $this->redirectToRoute('index');
        }

        $configs = $this->repo->findAll();
        $formData = [
            'configs' => $configs
        ];

        $form = $this->createForm(ModifyConfigurationType::class, $formData);

        return $this->render('configuration/basic/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/configuration/change", name="change_configuration")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function change(Request $request)
    {
        $configs = $this->repo->findAll();
        $formData = [
            'configs' => $configs
        ];

        $form = $this->createForm(ModifyConfigurationType::class, $formData);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($formData['configs'] as $formConfig) {
                    $storedConfig = $this->getConfigsByName($configs, $formConfig->getName());
                    if (null !== $storedConfig) {
                        $storedConfig->setValue($formConfig->getValue());
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Configuration changed.');

            return $this->redirectToRoute('basic_configuration');
        }

        return $this->redirectToRoute('basic_configuration');
    }

    /**
     * @param ConfigurationInterface[] $configs
     * @param $name
     * @return ConfigurationInterface|null
     */
    protected function getConfigsByName(array $configs, $name)
    {
        foreach ($configs as $config) {
            if ($config->getName() === $name) {
                return $config;
            }
        }

        return null;
    }
}
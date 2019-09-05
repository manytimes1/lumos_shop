<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\QuantityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     * @param QuantityRepository $quantityRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(CartRepository $cartRepository, QuantityRepository $quantityRepository)
    {
        $user = $this->getUser();
        $cartProducts = $cartRepository->findBy(['user' => $user]);
        $quantities = $quantityRepository->findAll();

        $totalPrice = 0;

        for ($i = 0; $i < count($cartProducts); $i++) {
            $totalPrice += $cartProducts[$i]->totalPrice();
        }

        return $this->render('cart/index.twig', [
            'carts' => $cartProducts,
            'quantities' => $quantities,
            'total' => $totalPrice
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addProduct(Product $product, CartRepository $cartRepository)
    {
        if (!$product->getIsAvailable()) {
            return $this->redirectToRoute('index');
        }

        $user = $this->getUser();
        $cart = $cartRepository->findOneBy([
            'product' => $product,
            'user' => $user
        ]);

        if (null == $cart) {
            $cart = new Cart();
            $cart->setProduct($product);
            $cart->setUser($user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_product_remove")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeProduct(CartRepository $cartRepository, Product $product)
    {
        $user = $this->getUser();
        $cartProduct = $cartRepository->findOneBy([
            'product' => $product,
            'user' => $user
        ]);

        if (null !== $cartProduct) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cartProduct);
            $em->flush();
        }

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/cart/update/{id}", name="update_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param Cart $cart
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateCart(Request $request, Cart $cart)
    {
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Cart updated successfully.');

        return $this->redirectToRoute('cart_index');
    }
}
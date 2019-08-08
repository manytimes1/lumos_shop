<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index(CartRepository $cartRepository)
    {
        $user = $this->getUser();
        $cartProducts = $cartRepository->findBy(['user' => $user]);

        $totalPrice = 0;

        for ($i = 0; $i < count($cartProducts); $i++) {
            $totalPrice += $cartProducts[$i]->totalPrice();
        }

        return $this->render('cart/index.twig', [
            'carts' => $cartProducts,
            'total' => $totalPrice
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     *
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

        $cart->setOrderQuantity(1);
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
}
<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
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

    /**
     * @Route("/cart/buyAll", name="buy_all_products")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param CartRepository $cartRepository
     * @param ProductRepository $productRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buyAllProducts(CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $products = $productRepository->findAll();
        $cartProducts = $cartRepository->findBy([
            'product' => $products,
            'user' => $user
        ]);

        $em = $this->getDoctrine()->getManager();

        foreach ($cartProducts as $cartProduct) {
            if ($cartProduct->totalPrice() > $profile->getCash()) {
                $this->addFlash('danger', "You don't have enough money to this action.");

                return $this->redirectToRoute('cart_index');
            }

            $remainingCash = $profile->getCash() - $cartProduct->totalPrice();
            $profile->setCash($remainingCash);

            /** @var Cart $cartProduct */
            $product = $cartProduct->getProduct();
            $remainingQuantity = $product->getQuantity() - $cartProduct->getOrderQuantity();
            $product->setQuantity($remainingQuantity);

            if ($product->getQuantity() === 0) {
                $product->setIsAvailable(0);
            }

            $order = new Order();
            $order->setProduct($product);
            $order->setUser($user);

            $em->persist($order);
            $em->remove($cartProduct);
        }

        $em->flush();

        return $this->redirectToRoute('index');
    }
}
<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Meal;
use App\Form\Type\CommandType;
use App\Repository\CommandRepository;
use App\Service\AjaxService;
use App\Service\CartService;
use App\Service\Mailer;
use App\Service\Recaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/command", name="command_")
 */
class CommandController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CommandRepository $commandRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, CartService $cartService, Recaptcha $recaptcha, Mailer $mailer, AjaxService $ajaxService): Response
    {
        $command = new Command();
        $form = $ajaxService->command_meal($command);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $cart = $cartService->getFullCart();
            foreach ($cart as $value) {
                /** @var \App\Entity\Meal $meal */
                $meal = $value['product'];

                $min = $meal->getProvider()->getMinPriceDelivery();
                if ($value['quantity'] * $meal->getPrice() < $min) {
                    $form->get('stock')->addError(new FormError('Le prix minimum de livraison pour '.$meal->getName().' est de '.$min.' XPF'));

                    break;
                }
                if ($value['quantity'] > $meal->getStock()) {
                    $form->get('min')->addError(new FormError('Le produit est en rupture de stock : '.$meal->getName()));

                    break;
                }
            }

            if ($g = $form->get('recaptcha')->getData()) {
                if (!$recaptcha->captchaverify($g)->success) {
                    $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $details = [];

                foreach ($cart as $value) {
                    /** @var \App\Entity\Meal $meal */
                    $meal = $value['product'];

                    $meal->commandPlus()
                        ->setStock($meal->getStock() - $value['quantity'])
                    ;

                    $command->addMeal($meal)
                        ->addProvider($meal->getProvider())
                    ;

                    $details[] = [
                        [
                            'product' => $meal->getId(),
                            'quantity' => $value['quantity'],
                        ],
                    ];
                }

                $command->setPrice($cartService->getTotal())
                    ->setDetails($details)
                ;

                $entityManager->persist($command);
                $entityManager->flush();

                $string = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

                $command->setReference('REF #'.$command->getId().'-'.substr($string, 25).'-'.substr($string, 1, 3));
                $entityManager->flush();

                $mailer->sendCommand($command);

                $this->addFlash('success', 'Votre commande a été envoyée !');
                $cartService->reset();

                return new Response($this->generateUrl('meal_index'), Response::HTTP_CREATED);
            }

            return $this->render('command/_form.html.twig', [
                'form' => $form->createView(),
            ], new Response('error', Response::HTTP_BAD_REQUEST));
        }

        return $this->render('command/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('command_index');
        }

        return $this->render('command/edit.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Command $command): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($command);
            $entityManager->flush();
        }

        return $this->redirectToRoute('command_index');
    }

    /**
     * @Route("/cart/all", name="cart")
     */
    public function cart_index(CartService $cartService)
    {
        return $this->render('command/cart.html.twig', [
            'cart' => $cartService->getFullCart(),
            'prices' => $cartService->getTotal(),
        ]);
    }

    /**
     * @Route("/cart/items", name="cart_items")
     */
    public function cart_items(CartService $cartService)
    {
        return $this->render('command/cart_items.html.twig', [
            'items' => count($cartService->getFullCart()),
        ]);
    }

    /**
     * @Route("/cart/prices", name="cart_prices")
     */
    public function cart_prices(CartService $cartService)
    {
        return $this->render('command/cart_prices.html.twig', [
            'prices' => $cartService->getTotal(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="addToCart", methods={"POST"})
     */
    public function add_to_cart(Meal $meal, CartService $cart, Recaptcha $recaptcha, AjaxService $ajaxService, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $form = $ajaxService->cart_form($meal);
            $form->handleRequest($request);

            $f = false;
            if ($g = $form->get('recaptcha')->getData()) {
                $f = $recaptcha->captchaverify($g)->success;
            }

            if ($form->isSubmitted() && !$f) {
                $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
            }

            if ($form->isSubmitted() && $form->isValid() && $f) {
                for ($i = 0; $i < $form->get('quantity')->getData(); ++$i) {
                    $cart->add($meal->getId());
                }

                return new Response('success', Response::HTTP_CREATED);
            }
        }

        return new Response('error', Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @Route("/cart/remove/{id}", name="removeFromCart", methods={"DELETE"})
     */
    public function remove_from_cart(Meal $meal, CartService $cart, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$meal->getId(), $request->request->get('_token'))) {
            $cart->remove($meal->getId());
        }

        return $this->redirectToRoute('command_cart');
    }
}

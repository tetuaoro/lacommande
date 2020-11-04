<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Meal;
use App\Message\SendEmailMessage;
use App\Repository\CommandRepository;
use App\Repository\ProviderRepository;
use App\Service\AjaxService;
use App\Service\CartService;
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
    public function new(Request $request, CartService $cartService, Recaptcha $recaptcha, ProviderRepository $providerRepo, AjaxService $ajaxService): Response
    {
        $command = new Command();
        $form = $ajaxService->command_meal($command, $this->getUser());

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);

            $checked = $cartService->checkMinDelivery();
            if (!$checked['check']) {
                $form->get('stock')->addError(new FormError('Le prix minimum de commande pour '.$checked['provider']->getName().' est de '.$checked['provider']->getMinPriceDelivery().' XPF'));
            }

            $checked = $cartService->checkOpenHours($form->get('commandAt')->getData());
            if (!$checked['check']) {
                $form->get('openHours')->addError(new FormError('Verifier votre date pour '.$checked['provider']->getName().' (heure d\'ouverture, temps minimum pour commander, produit limité dans le temps...)'));
            }

            if ($g = $form->get('recaptcha')->getData()) {
                if (!$recaptcha->captchaverify($g)->success) {
                    $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $details = [];

                foreach ($cartService->getFullCart() as $cart) {
                    /** @var \App\Entity\Meal $meal */
                    $meal = $cart['product'];

                    $meal->commandPlus()
                        ->setStock($meal->getStock() - $cart['quantity'])
                    ;

                    $command->addMeal($meal)
                        ->addProvider($meal->getProvider())
                    ;

                    $details[] = [
                        $meal->getId() => $cart['quantity'],
                    ];
                }

                $command->setPrice($cartService->getTotal())
                    ->setDetails(array_replace(...$details))
                    ->setConfirmDelete(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='))
                ;

                /** @var \App\Entity\User $user */
                $user = $this->getUser();
                if ($user && $user->getLambda()) {
                    $command->setLambda($user->getLambda());
                }

                $entityManager->persist($command);
                $entityManager->flush();

                $string = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

                $command->setReference($command->getId().'-'.substr($string, 24).'-'.substr($string, 1, 2));
                $this->dispatchMessage(new SendEmailMessage(SendEmailMessage::SEND_COMMAND, 1, $command->getId(), true));

                $this->addFlash('success', 'Votre commande a été envoyée !');

                foreach ($cartService->getCartByProvider() as $id => $tabs) {
                    /** @var \App\Entity\Provider $provider */
                    $provider = $providerRepo->find($id);

                    if ($provider->getAutoCommandValidation()) {
                        $this->dispatchMessage(new SendEmailMessage(SendEmailMessage::VALID_COMMAND, $provider->getUser()->getId(), $command->getId(), true));

                        $command->setValidation($provider, true);
                    }
                }
                $entityManager->flush();

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
     * @Route("/delete/{id}-{token}", name="delete", methods={"GET"}, requirements={"token": "[a-zA-Z0-9\-\_]*"})
     */
    public function delete(string $token, Command $command): Response
    {
        if ($command->getConfirmDelete() == $token) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($command);
            $entityManager->flush();
            $this->addFlash('success', 'La commande a été supprimée !');
        } else {
            $this->addFlash('danger', 'Impossible de supprimer cette commande. Prevenez directement les commerçants !');
        }

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/cart/all", name="cart")
     */
    public function cart_index(CartService $cartService)
    {
        return $this->render('command/cart.html.twig', [
            'cart' => $cartService->getFullCart(),
            'cart2' => $cartService->getFullCartByProvider(),
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

            if ($g = $form->get('recaptcha')->getData()) {
                if (!$recaptcha->captchaverify($g)->success) {
                    $form->get('recaptcha')->addError(new FormError('Recaptcha : êtes-vous un robot ?'));
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $cart->add($meal->getId(), $form->get('quantity')->getData());

                return new Response('success', Response::HTTP_CREATED);
            }

            return new Response('error', Response::HTTP_BAD_REQUEST);
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

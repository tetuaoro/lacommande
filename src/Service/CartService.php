<?php

namespace App\Service;

use App\Repository\MealRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $mealRepository;

    public function __construct(SessionInterface $session, MealRepository $mealRepository)
    {
        $this->session = $session;
        $this->mealRepository = $mealRepository;
    }

    public function add(int $id)
    {
        $cart = $this->session->get('cart', []);

        if (empty($cart[$id])) {
            $cart[$id] = 0;
        }

        ++$cart[$id];

        $this->session->set('cart', $cart);
    }

    public function remove(int $id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $this->session->set('cart', $cart);
    }

    public function reset()
    {
        $this->session->set('cart', []);
    }

    public function getFullCart(): array
    {
        $cart = $this->session->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->mealRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        return $cartWithData;
    }

    public function getTotal(): float
    {
        $cartWithData = $this->getFullCart();

        $total = 0;

        foreach ($cartWithData as $couple) {
            $total += $couple['product']->getPrice() * $couple['quantity'];
        }

        return $total;
    }
}

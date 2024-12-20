<?php

namespace App\Service;

use App\Entity\Command;
use App\Repository\MealRepository;
use App\Repository\ProviderRepository;
use DateTime;
use Spatie\OpeningHours\OpeningHours;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $mealRepository;
    protected $providerRepo;

    public function __construct(SessionInterface $session, ProviderRepository $providerRepository, MealRepository $mealRepository)
    {
        $this->session = $session;
        $this->mealRepository = $mealRepository;
        $this->providerRepo = $providerRepository;
    }

    public function add(int $id, int $quantity)
    {
        $cart = $this->session->get('cart', []);

        if (empty($cart[$id])) {
            $cart[$id] = 0;
        }

        for ($i = 0; $i < $quantity; ++$i) {
            ++$cart[$id];
        }

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

    public function getCartByProvider(): array
    {
        $providers = [];

        foreach ($this->getFullCart() as $value) {
            /** @var \App\Entity\Meal $meal */
            $meal = $value['product'];

            $providers[$meal->getProvider()->getId()][] = $value;
        }

        return $providers;
    }

    public function checkMinDelivery(): array
    {
        foreach ($this->getCartByProvider() as $id => $tabs) {
            $provider = $this->providerRepo->find($id);
            if ($provider->getForceDelivery()) {
                $total = 0;
                foreach ($tabs as $couple) {
                    $total += $couple['product']->getPrice() * $couple['quantity'];
                }
                if ($total < $provider->getMinPriceDelivery()) {
                    return ['check' => false, 'provider' => $provider];
                }
            }
        }

        return ['check' => true];
    }

    public function checkOpenHours(DateTime $dateTime): array
    {
        $timezone = new \DateTimeZone('Pacific/Honolulu');
        $date = new \DateTime('now', $timezone);

        foreach ($this->getCartByProvider() as $id => $tabs) {
            $provider = $this->providerRepo->find($id);

            $openingHours = OpeningHours::create($provider->getOpenHours(), $timezone);

            if ($openingHours->isClosedAt($dateTime) || $date->modify('+'.$provider->getMinTimeCommand().' minutes') > $dateTime) {
                return ['check' => false, 'provider' => $provider];
            }
        }

        return ['check' => true];
    }

    public function getFullCartByProvider(): array
    {
        $cart = [];

        foreach ($this->getCartByProvider() as $id => $tabs) {
            $total = 0;
            $meals = [];
            foreach ($tabs as $couple) {
                $total += $couple['product']->getPrice() * $couple['quantity'];
                $meals[] = $couple;
            }
            $cart[] = [
                'price' => $total,
                'provider' => $this->providerRepo->find($id),
                'meals' => $meals,
            ];
        }

        return $cart;
    }

    public function getFullCartFromCommand(Command $command): array
    {
        $cart = [];
        $TADA = $this->getCartFromCommand($command);

        foreach ($TADA as $provider_id => $meals_) {
            $total = 0;
            $meals = [];
            foreach ($meals_ as $couple) {
                $total += $couple['product']->getPrice() * $couple['quantity'];
                $meals[] = $couple;
            }
            $cart[] = [
                'price' => $total,
                'provider' => $this->providerRepo->find($provider_id),
                'meals' => $meals,
            ];
        }

        return $cart;
    }

    private function getCartFromCommand(Command $command): array
    {
        $providers = [];

        foreach ($command->getDetails() as $meal_id => $quantity) {
            $meal = $this->mealRepository->find($meal_id);

            $providers[$meal->getProvider()->getId()][] = [
                'product' => $meal,
                'quantity' => $quantity,
            ];
        }

        return $providers;
    }
}

<?php
namespace App\Basket;

use App\Auth\User;
use App\Basket\Table\BasketTable;
use App\Basket\Table\OrderTable;
use App\Shop\Table\StripeUserTable;
use Framework\API\Stripe;
use Mpociot\VatCalculator\VatCalculator;
use Stripe\Card;
use Stripe\Customer;

class PurchaseBasket
{
      private $orderTable;
    private $basketTable;
    private $stripe;
    private $stripeUserTable;

    public function __construct(
        OrderTable $orderTable,
        BasketTable $basketTable,
        Stripe $stripe,
        StripeUserTable $stripeUserTable
    ) {
        $this->orderTable = $orderTable;
        $this->basketTable = $basketTable;
        $this->stripe = $stripe;
        $this->stripeUserTable = $stripeUserTable;
    }
    
    /**
     * Génère l'achat du produit en utilisant stripe
     *
     * @param  Basket $basket
     * @param  User $user
     * @param  string $token
     * @return void
     */
    public function process(Basket $basket, User $user, string $token)
    {
        //Calculer le prix TTC
        $card = $this->stripe->getCardFromClient($token);
        $this->basketTable->hydrateBasket($basket);
        $vatCalculator = new VatCalculator();
        $grossPrice = $vatCalculator->calculate($basket->getPrice(), $card->country);
        $taxRate = $vatCalculator->getTaxRate();
        //Créer ou récupérer le customer de l'utilisateur. Le customer représente l'utilisateur au niveau de l'api
        $customer = $this->findCustomerForUser($user, $token);
        //Créer ou récupérer la carte de l'utilisateur
        $card = $this->getMatchingCard($customer, $card);
        if ($card === null) {
            $card = $this->stripe->createCardForCustomer($customer, $token);
        }
        //facturer l'utilisateur (créer la charge)
        $charge = $this->stripe->createCharge([
            "amount" => $grossPrice * 100,
            'currenty' => 'eur',
            'source' => $card->id,
            "description" => "Achat sur monSite.com"
        ]);
        //return $charge->id;
        //Sauvegarger la transaction
        $this->orderTable->createFromBasket($basket, [
            'user_id' => $user->getId(),
            'vat' => $taxRate,
            'created_at' => date('Y-m-d H:i:s'),
            'stripe_id' => $charge->id
        ]);
    }
    
    /**
     * Vérifie si la carte existe déjà
     *
     * @param  Customer $customer
     * @param  Card $card
     * @return bool
     */
    private function getMatchingCard(Customer $customer, Card $card): ?Card
    {
        foreach ($customer->sources->show_source as $data) {
            $data->fingerprint === $card->fingerprint;
            return $data;
        }
        return null;
    }
    
    /**
     * Génère le client à partir de l'utilisateur
     *
     * @param  User $user
     * @param  string $token
     * @return Customer
     */
    public function findCustomerForUser(User $user, string $token): Customer
    {
        $customerId = $this->stripeUserTable->findCustomerForUser($user);
        if ($customerId) {
            $customer = $this->stripe->getCustomer($customerId);
        } else {
            $customer = $this->stripe->createCustomer([
                'email' => $user->getEmail(),
                'source' => $token
            ]);
            $this->stripeUserTable->insert([
                'user_id' => $user->getId(),
                'customer_id' => $customer->id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        return $customer;
    }
}

<?php
namespace App\Shop;

use App\Auth\User;
use App\Shop\Entity\Product;
use App\Shop\Exception\AlreadyPurcharsedException;
use App\shop\Table\PurchaseTable;
use App\Shop\Table\StripeUserTable;
use Framework\API\Stripe;
use Mpociot\VatCalculator\VatCalculator;
use Stripe\Card;
use Stripe\Customer;

class PurchaseProduct
{
    /**
     * purchaseTable
     *
     * @var PurchaseTable
     */
    private $purchaseTable;

    /**
     * stripe
     *
     * @var stripe
     */
    private $stripe;

    /**
     * stripe
     *
     * @var stripeUserTable
     */
    private $stripeUserTable;

    public function __construct(
        PurchaseTable $purchaseTable,
        stripe $stripe,
        StripeUserTable $stripeUserTable
    ) {
        $this->purchaseTable = $purchaseTable;
        $this->stripe = $stripe;
        $this->stripeUserTable = $stripeUserTable;
    }
    
    /**
     * Génère l'achat du produit en utilisant stripe
     *
     * @param  Product $product
     * @param  User $user
     * @param  string $token
     * @return void
     */
    public function process(Product $product, User $user, string $token)
    {
        //vérifie que le produit n'a pas déjà été acheté
        if ($this->purchaseTable->findFor($product, $user) !== null) {
            throw new AlreadyPurcharsedException();
        }
        //Calculer le prix TTC
        $card = $this->stripe->getCardFromClient($token);
        $vatCalculator = new VatCalculator();
        $grossPrice = $vatCalculator->calculate($product->getPrice(), $card->country);
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
            'amount' => $grossPrice * 100,
            'currency' => 'usd',
            'source' => $card->id,
            'customer' => $customer->id,
            'description' => "Achat sur monsite.com {$product->getName()}",
        ]);
        //return $charge->id;
        //Sauvegarger la transaction
        $this->purchaseTable->insert([
            "user_id" => $user->getId(),
            'product_id' => $product->getId(),
            'price' => $product->getPrice(),
            'vat' => $taxRate,
            'country' => $card->country,
            'create_at' => date('Y-m-d H:i:s'),
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

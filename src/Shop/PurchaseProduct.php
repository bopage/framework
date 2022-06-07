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
    private $purchaseTable;
    private $stripe;
    private $stripeUserTable;

    public function __construct(
        PurchaseTable $purchaseTable,
        Stripe $stripe,
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
        //vérifier que l'utilisateur n'a pas acheté le produit
        if ($this->purchaseTable->findFor($product, $user) !== null) {
            throw new AlreadyPurcharsedException();
        }
        //Calculer le prix TTC
        $card = $this->stripe->getCardFromClient($token);
        $grossPrice = (new VatCalculator())->calculate($product->getPrice(), $card->country);
        //Créer ou récupérer le customer de l'utilisateur. Le customer représente l'utilisateur au niveau de l'api
        $customer = $this->findCustomerForUser($user, $token);
        //Créer ou récupérer la carte de l'utilisateur
        if (!$this->hasCard($customer, $card)) {
            $card = $this->stripe->createCardForCustomer($customer, $token);
        }
        //facturer l'utilisateur (créer la charge)
        $charge = $this->stripe->createCharge([
            "amount" => $grossPrice * 100,
            'currenty' => 'eur',
            'source' => $card->id,
            "description" => "Achat sur monSite.com $product->getId"
        ]);
        //return $charge->id;
        //Sauvegarger la transaction
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => $product->getPrice(),
            'vat' => ,
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
    private function hasCard(Customer $customer, Card $card): bool
    {
        $fingerprints = array_map(function ($source) {
            return $source->fingerprint;
        }, $customer->sources->all());
        return in_array($card->fingerprint, $fingerprints);
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

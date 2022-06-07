<?php
namespace Framework\API;

use Stripe\Card;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe as StripeStripe;
use Stripe\Token;

class Stripe
{
    public function __construct(string $token)
    {
        StripeStripe::setApiKey($token);
    }

    public function getCardFromClient(string $token): Card
    {
        return Token::retrieve($token)->card;
    }

    public function getCustomer($customerId): Customer
    {
        return Customer::retrieve($customerId);
    }

    public function createCustomer(array $params): Customer
    {
        return Customer::create($params);
    }

    public function createCardForCustomer(Customer $customer, string $token)
    {
        return $customer->sources->create(['source' => $token]);
    }

    public function createCharge(array $params)
    {
        return Charge::create($params);
    }
}

<?php

namespace Tests\App\Shop;

use App\Auth\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use App\Shop\Exception\AlreadyPurcharsedException;
use App\Shop\PurchaseProduct;
use App\shop\Table\PurchaseTable;
use App\Shop\Table\StripeUserTable;
use Framework\API\Stripe;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Argument\Token\LogicalAndToken;
use Prophecy\PhpUnit\ProphecyTrait;
use Stripe\Card;
use Stripe\Charge;
use Stripe\Collection;
use Stripe\Customer;

class PurchaseProductTest extends TestCase
{
    use ProphecyTrait;

    private $purchaseTable;

    /**
     * purchaseProduct
     *
     * @var PurchaseProduct
     */
    private $purchaseProduct;

    private $stripe;

    private $stripeUserTable;

    protected function setUp(): void
    {
        $this->purchaseTable = $this->prophesize(PurchaseTable::class);
        $this->stripe = $this->prophesize(Stripe::class);
        $this->stripeUserTable = $this->prophesize(StripeUserTable::class);
        $this->purchaseProduct = new PurchaseProduct(
            $this->purchaseTable->reveal(),
            $this->stripe->reveal(),
            $this->stripeUserTable->reveal()
        );

        $this->stripe->getCardFromClient(Argument::any())->will(function ($args) {
            $card = new Card();
            $card->fingerprint = "a";
            $card->country = $args[0];
            $card->id = "tokenCard";
            return $card;
        });
    }

    public function testAlreadyPurchaseProduct()
    {
        $product = $this->makeProduct();
        $user = $this->makeUser();
        $token = 'token';
        $this->purchaseTable->findFor($product, $user)
            ->shouldBeCalled()
            ->willReturn($this->makePurchase());
        $this->expectException(AlreadyPurcharsedException::class);
        $this->purchaseProduct->process($product, $user, $token);
    }

    public function testPurchaseFrance()
    {
        $customerId = "cuz_12345";
        $token = 'FR';
        $product = $this->makeProduct();
        $user = $this->makeUser();
        $card = $this->makeCard();
        $customer = $this->makeCustomer();
        $charge = $this->makeCharge();

        $this->purchaseTable->findFor($product, $user)->willReturn(null);
        $this->stripeUserTable->findCustomerForUser($user)->willReturn($customerId);
        $this->stripe->getCustomer($customerId)->willReturn($customer);
        $this->stripe->createCardForCustomer($customer, $token)
            ->shouldBeCalled()
            ->willReturn($card);
        $this->stripe->createCharger(new LogicalAndToken([
            Argument::withEntry('amount', 6000),
            Argument::withEntry('source', $card->id)
        ]))->shouldBecalled()
            ->willReturn($charge);
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => 50.00,
            'vat' => 20,
            'created_at' => date('Y-m-d H:i:s'),
            'stripe_id' => $charge->id
        ])->shouldBecalled();
        // on lance l'achat
        $id = $this->purchaseProduct->process($product, $user, 'FR');
        $this->assertEquals($charge->id, $id);
    }

    public function testPurchaseUS()
    {
        $customerId = "cuz_12345";
        $token = 'US';
        $product = $this->makeProduct();
        $user = $this->makeUser();
        $card = $this->makeCard();
        $customer = $this->makeCustomer();
        $charge = $this->makeCharge();

        $this->purchaseTable->findFor($product, $user)->willReturn(null);
        $this->stripeUserTable->findCustomerForUser($user)->willReturn($customerId);
        $this->stripe->getCustomer($customerId)->willReturn($customer);
        $this->stripe->createCardForCustomer($customer, $token)
            ->shouldBeCalled()
            ->willReturn($card);
        $this->stripe->createCharger(new LogicalAndToken([
            Argument::withEntry('amount', 5000),
            Argument::withEntry('source', $card->id)
        ]))->shouldBecalled()
            ->willReturn($charge);
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => 50.00,
            'vat' => 00,
            'created_at' => date('Y-m-d H:i:s'),
            'stripe_id' => $charge->id
        ])->shouldBecalled();
        // on lance l'achat
        $id = $this->purchaseProduct->process($product, $user, 'FR');
        $this->assertEquals($charge->id, $id);
    }

    public function testPurchaseFranceWithExistingCard()
    {
        $customerId = "cuz_12345";
        $token = 'FR';
        $product = $this->makeProduct();
        $user = $this->makeUser();
        $card = $this->makeCard();
        $cardToken = $this->stripe->reveal()->getCardFromToken($token);
        $customer = $this->makeCustomer([$card]);
        $charge = $this->makeCharge();

        $this->purchaseTable->findFor($product, $user)->willReturn(null);
        $this->stripeUserTable->findCustomerForUser($user)->willReturn($customerId);
        $this->stripe->getCustomer($customerId)->willReturn($customer);
        $this->stripe->createCardForCustomer($customer, $token)->shouldNotBeCalled();
        $this->stripe->createCharger(new LogicalAndToken([
            Argument::withEntry('amount', 6000),
            Argument::withEntry('source', $cardToken->id)
        ]))->shouldBecalled()
            ->willReturn($charge);
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => 50.00,
            'vat' => 20,
            'created_at' => date('Y-m-d H:i:s'),
            'stripe_id' => $charge->id
        ])->shouldBecalled();
        // on lance l'achat
        $id = $this->purchaseProduct->process($product, $user, 'FR');
        $this->assertEquals($charge->id, $id);
    }

    public function testPurchaseFranceWithNotCustomer()
    {
        $customerId = "cuz_12345";
        $token = 'FR';
        $product = $this->makeProduct();
        $user = $this->makeUser();
        $card = $this->stripe->reveal()->getCardFromToken($token);
        $customer = $this->makeCustomer([$card]);
        $charge = $this->makeCharge();

        $this->purchaseTable->findFor($product, $user)->willReturn(null);
        $this->stripeUserTable->findCustomerForUser($user)->willReturn(null);
        $this->stripeUserTable->insert([
            'user_id' => $user->getId(),
            'customer_id' => $customer->id,
            'created_at' => date('Y-m-d H:i:s')
        ])->shouldBecalled();
        $this->stripe->createCustomer([
            'email' => $user->getEmail(),
            'source' => $token
        ])->shouldBecalled()->willReturn($customer);
        $this->stripe->createCardForCustomer($customer, $token)->shouldNotBeCalled();
        $this->stripe->createCharger(new LogicalAndToken([
            Argument::withEntry('amount', 6000),
            Argument::withEntry('source', $card->id)
        ]))->shouldBecalled()
            ->willReturn($charge);
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => 50.00,
            'vat' => 20,
            'created_at' => date('Y-m-d H:i:s'),
            'stripe_id' => $charge->id
        ])->shouldBecalled();
        // on lance l'achat
        $id = $this->purchaseProduct->process($product, $user, 'FR');
        $this->assertEquals($charge->id, $id);
    }

    private function makePurchase(): Purchase
    {
        $purchase = new Purchase;
        $purchase->setId(3);
        return $purchase;
    }

    private function makeUser(): User
    {
        $user = new User;
        $user->setId(4);
        return $user;
    }

    private function makeProduct(): Product
    {
        $product = new Product;
        $product->setId(4);
        $product->setPrice(50);
        return $product;
    }

    private function makeCustomer(array $source = []): Customer
    {
        $customer = new Customer();
        $collection = $this->prophesize(Collection::class);
        $collection->all()->willReturn($source);
        $customer->sources = $collection->reveal();
        return $customer;
    }

    private function makeCard(): Card
    {
        $card = new Card();
        $card->id = "card_123";
        $card->fingerprint = 'a';
        return $card;
    }

    private function makeCharge(): Charge
    {
        $charge = new Charge();
        $charger->id = "aze_123";
        return $charge;
    }
}

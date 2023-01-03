<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\User;
use App\Services\CartService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;
use Tests\Mocks\ExternalAPIMockFactory;
use Tests\Mocks\ExternalAPIMockType;
use function app;
use function now;

class CartServiceTest extends TestCase
{

    use RefreshDatabase;
 
    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @var CartService
     */
    protected $service;

    /**
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::find(1);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::Functional);
        $this->service = app(CartService::class, compact('externalAPIService'));
    }

    /**
     * Tests the method getActiveCart when the user does not have an active cart
     *
     * @return void
     */
    public function testGetActiveCartOfUserWithoutCart()
    {
        $return = $this->service->getActiveCart($this->user);

        $this->assertInstanceOf(Cart::class, $return, 'The active cart did not return a Cart object');
        $this->assertEmpty($return->items, 'The active cart has items');
    }

    /**
     * Tests the method getActiveCart when the user already has an active cart
     *
     * @return void
     */
    public function testGetActiveCartOfUserWithACart()
    {
        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $return = $this->service->getActiveCart($this->user);

        $this->assertInstanceOf(Cart::class, $return, 'The active cart did not return a Cart object');
        $this->assertEquals($cart->id, $return->id, 'The active cart is different than the one created last');
    }

    /**
     * Tests the method getActiveCart when the user already has an active cart
     *
     * @return void
     */
    public function testGetActiveCartOfUserWithAClosedCart()
    {
        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);
        $cart->closed_at = now();
        $cart->save();

        $return = $this->service->getActiveCart($this->user);

        $this->assertInstanceOf(Cart::class, $return, 'The active cart did not return a Cart object');
        $this->assertNotEquals($cart->id, $return->id, 'The active cart is equals than the one created last');
    }

    /**
     * Tests the method addItemToCart when the item is not in the cart
     *
     * @return void
     */
    public function testAddNotExistingItemToCart()
    {
        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $cart = $this->service->addItemToCart($request, $this->user);

        $this->assertInstanceOf(Cart::class, $cart, 'Adding an item did not return a Cart object');
        $this->assertCount(1, $cart->items, 'The quantity of items of the resulting cart is different than the expected');
        $this->assertEquals('test', $cart->items->first()->product_id, 'The product ID of the item inserted is different than the expected');
        $this->assertEquals(1, $cart->items->first()->quantity, 'The quantity of the item inserted is different than the expected');
    }

    /**
     * Tests the method addItemToCart when the item is not in the cart and there is an error with the external API
     *
     * @return void
     */
    public function testAddNotExistingItemToCartWithError()
    {
        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NotFound);
        $this->service = app(CartService::class, compact('externalAPIService'));

        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        // Normally we should use $this->expectException but I need to test how the items are in the cart
        $exceptionWasThrown = false;
        try
        {
            $this->service->addItemToCart($request, $this->user);
        }
        catch (Exception $e)
        {
            $exceptionWasThrown = true;
        }
        $this->assertTrue($exceptionWasThrown, 'Excption was not thrown');

        $this->user->refresh();
        $cart = $this->user->carts()->latest()->first();

        $this->assertInstanceOf(Cart::class, $cart, 'Adding an item did not create a Cart object before the exception');
        $this->assertEmpty($cart->items, 'The item was not deleted after the exception');
    }

    /**
     * Tests the method addItemToCart when the item is already in the cart
     *
     * @return void
     */
    public function testAddExistingItemToCart()
    {
        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $updatedCart = $this->service->addItemToCart($request, $this->user);

        $this->assertInstanceOf(Cart::class, $updatedCart, 'Adding an item did not return a Cart object');
        $this->assertCount(1, $updatedCart->items, 'The quantity of items of the resulting cart is different than the expected');
        $this->assertEquals('test', $updatedCart->items->first()->product_id, 'The product ID of the item inserted is different than the expected');
        $this->assertEquals(2, $updatedCart->items->first()->quantity, 'The quantity of the item inserted is different than the expected');
    }

    /**
     * Tests the method addItemToCart when the item is already in the cart and there is an error with the external API
     *
     * @return void
     */
    public function testAddExistingItemToCartWithError()
    {
        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NotFound);
        $this->service = app(CartService::class, compact('externalAPIService'));

        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        // Normally we should use $this->expectException but I need to test how the items are in the cart
        $exceptionWasThrown = false;
        try
        {
            $this->service->addItemToCart($request, $this->user);
        }
        catch (Exception $e)
        {
            $exceptionWasThrown = true;
        }
        $this->assertTrue($exceptionWasThrown, 'Excption was not thrown');

        $this->user->refresh();
        $updatedCart = $this->user->carts()->latest()->first();

        $this->assertInstanceOf(Cart::class, $cart, 'Adding an item deleted the Cart object after the exception');
        $this->assertCount(1, $updatedCart->items, 'The quantity of items of the resulting cart is different than the expected');
        $this->assertEquals('test', $updatedCart->items->first()->product_id, 'The product ID of the item inserted is different than the expected');
        $this->assertEquals(1, $updatedCart->items->first()->quantity, 'The last item was not deleted after the exception');
    }

    /**
     * Tests the method addItemToCart when the item is already in the cart
     *
     * @return void
     */
    public function testAddNotExistingItemToCartWithItems()
    {
        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test2',
            'quantity' => 1
        ]);

        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $updatedCart = $this->service->addItemToCart($request, $this->user);
        $lastItem = $updatedCart->items()->latest()->first();

        $this->assertInstanceOf(Cart::class, $updatedCart, 'Adding an item did not return a Cart object');
        $this->assertCount(2, $updatedCart->items, 'The quantity of items of the resulting cart is different than the expected');
        $this->assertEquals('test2', $lastItem->product_id, 'The product ID of the item inserted is different than the expected');
        $this->assertEquals(1, $lastItem->quantity, 'The quantity of the item inserted is different than the expected');
    }

    /**
     * Tests the method addItemToCart when the item is already in the cart and there is an error with the external API
     *
     * @return void
     */
    public function testAddNotExistingItemToCartWithItemsWithError()
    {
        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NotFound);
        $this->service = app(CartService::class, compact('externalAPIService'));

        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test2',
            'quantity' => 1
        ]);

        $request = Request::createFromGlobals();
        $request->merge([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        // Normally we should use $this->expectException but I need to test how the items are in the cart
        $exceptionWasThrown = false;
        try
        {
            $this->service->addItemToCart($request, $this->user);
        }
        catch (Exception $e)
        {
            $exceptionWasThrown = true;
        }
        $this->assertTrue($exceptionWasThrown, 'Excption was not thrown');

        $this->user->refresh();
        $updatedCart = $this->user->carts()->latest()->first();

        $this->assertInstanceOf(Cart::class, $cart, 'Adding an item deleted the Cart object after the exception');
        $this->assertCount(1, $updatedCart->items, 'The quantity of items of the resulting cart is different than the expected');
    }

    /**
     * Tests the method checkout when the user does not have an active cart
     *
     * @return void
     */
    public function testCheckoutCartOfUserWithoutCart()
    {
        $this->expectErrorMessage("You can't checkout without items.");
        $this->expectExceptionCode(400);

        $this->service->checkout($this->user);
    }

    /**
     * Tests the method checkout when the user has an active cart withou items
     *
     * @return void
     */
    public function testCheckoutCartOfUserWithCartWithoutItem()
    {
        $this->expectErrorMessage("You can't checkout without items.");
        $this->expectExceptionCode(400);

        $cart = $this->user->carts()->create();

        $this->service->checkout($this->user);
    }

    /**
     * Tests the method checkout when the user has an active cart with an item
     *
     * @return void
     */
    public function testCheckoutCartOfUserWithCartWithItem()
    {
        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $updatedCart = $this->service->checkout($this->user);

        $this->assertNotNull($updatedCart->closed_at, 'The cart was not closed by the checkout method');
    }

    /**
     * Tests the method checkout when the user has only a closed cart
     *
     * @return void
     */
    public function testCheckoutCartOfUserWithOnlyClosedCart()
    {
        $this->expectErrorMessage("You can't checkout without items.");
        $this->expectExceptionCode(400);

        $cart = $this->user->carts()->create();
        $cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);
        $cart->closed_at = now();
        $cart->save();

        $this->service->checkout($this->user);
    }

}

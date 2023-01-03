<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Mocks\ExternalAPIMockFactory;
use Tests\Mocks\ExternalAPIMockType;
use function app;

class ItemServiceTest extends TestCase
{

    use RefreshDatabase;
 
    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @var Cart
     */
    protected $cart;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);

        $this->cart = $user->carts()->create();
    }

    /**
     * Tests the method updatesItemFromAPI when the external API returns as expected
     *
     * @return void
     */
    public function testUpdatesItemFromApiWithoutErrors()
    {
        $item = $this->cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::Functional);
        $service = app(ItemService::class, compact('externalAPIService'));

        $service->updatesItemFromAPI($item);

        $item->refresh();

        $this->assertNotNull($item->individual_price);
        $this->assertNotNull($item->name);
    }

    /**
     * Tests the method updatesItemFromAPI when the external API returns with a not found error
     *
     * @return void
     */
    public function testUpdatesItemFromApiWithErrorNotFound()
    {
        $this->expectExceptionMessage('Item was not found on the external API');
        $this->expectExceptionCode(404);

        $item = $this->cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NotFound);
        $service = app(ItemService::class, compact('externalAPIService'));

        $service->updatesItemFromAPI($item);
    }

    /**
     * Tests the method updatesItemFromAPI when the external API returns with an unexpected error
     *
     * @return void
     */
    public function testUpdatesItemFromApiWithErrorUnexpected()
    {
        $this->expectExceptionMessage('There was an error getting the details of the product');

        $item = $this->cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::UnexpectedError);
        $service = app(ItemService::class, compact('externalAPIService'));

        $service->updatesItemFromAPI($item);
    }

    /**
     * Tests the method updatesItemFromAPI when the external API returns without price data
     *
     * @return void
     */
    public function testUpdatesItemFromApiWithErrorNoPrice()
    {
        $this->expectExceptionMessage('The API did not return the product price as expected');

        $item = $this->cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NoPrice);
        $service = app(ItemService::class, compact('externalAPIService'));

        $service->updatesItemFromAPI($item);
    }

    /**
     * Tests the method updatesItemFromAPI when the external API returns without name data
     *
     * @return void
     */
    public function testUpdatesItemFromApiWithErrorNoName()
    {
        $this->expectExceptionMessage('The API did not return the product name as expected');

        $item = $this->cart->items()->create([
            'product_id' => 'test',
            'quantity' => 1
        ]);

        $externalAPIService = ExternalAPIMockFactory::getMock(ExternalAPIMockType::NoName);
        $service = app(ItemService::class, compact('externalAPIService'));

        $service->updatesItemFromAPI($item);
    }

}

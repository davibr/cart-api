<?php

namespace App\Services;

use App\Interfaces\ExternalAPIInterface;
use App\Models\Item;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class ItemService
{

    /**
     * @var ExternalAPIService
     */
    private $externalAPIService;

    public function __construct(ExternalAPIInterface $externalAPIService)
    {
        $this->externalAPIService = $externalAPIService;
    }

    /**
     * Verifies if there's an error with the collected response.
     *
     * @param  \ArrayAccess  $response
     * @throws Exception if an error was found in the response
     */
    private function verifyAPIErrors($response)
    {
        $errors = Arr::get($response, 'errors');
        if ($errors)
        {
            foreach ($errors as $error)
            {
                if (Arr::get($error, 'message') == "404")
                {
                    throw new Exception('Item was not found on the external API', 404);
                }
            }
            throw new Exception('There was an error getting the details of the product');
        }

        if (!Arr::get($response, 'data.product.priceInfo.currentPrice.price'))
        {
            throw new Exception('The API did not return the product price as expected');
        }

        if (!Arr::get($response, 'data.product.name'))
        {
            throw new Exception('The API did not return the product name as expected');
        }
    }

    /**
     * Updates an item from the API.
     *
     * @param  Item  $item
     * @return Item
     */
    public function updatesItemFromAPI(Item $item)
    {
        $response = $this->externalAPIService->getDataFromAPI($item->product_id);

        $this->verifyAPIErrors($response);

        $item->individual_price = Arr::get($response, 'data.product.priceInfo.currentPrice.price');
        $item->name = Arr::get($response, 'data.product.name');
        $item->save();
    }

}

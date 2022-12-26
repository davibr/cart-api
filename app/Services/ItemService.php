<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use App\Models\Item;

class ItemService
{
    /**
     * Return the headers needed for the http request to the API
     * @return array
     */
    private function getHeaders() 
    {
        return [
            'X-RapidAPI-Key' => config('services.products.key'),
            'X-RapidAPI-Host' => config('services.products.host'),
        ];
    }


    /**
     * Verifies if there's an error with the collected response.
     *
     * @param  Response  $response
     * @throws Exception if an error was found in the response
     */
    private function verifyAPIErrors(Response $response)
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
     * Gets details from the external API about the product
     * 
     * @param string $productId
     * @return Response
     */
    private function getDataFromAPI($productId) {
        return Http::withHeaders($this->getHeaders())
            ->get(config('services.products.url'), [
                'usItemId' => $productId
            ]);
    }

    /**
     * Updates an item from the API.
     *
     * @param  Item  $item
     * @return Item
     */
    public function updatesItemFromAPI(Item $item)
    {
        $response = $this->getDataFromAPI($item->product_id);

        $this->verifyAPIErrors($response);
        
        $item->individual_price = Arr::get($response, 'data.product.priceInfo.currentPrice.price');
        $item->name = Arr::get($response, 'data.product.name');
        $item->save();
    }
}
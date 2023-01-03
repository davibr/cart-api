<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Interfaces\ExternalAPIInterface;
use function config;

class ExternalAPIService implements ExternalAPIInterface
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
     * Gets details from the external API about the product
     *
     * @param string $productId
     * @return Response
     */
    public function getDataFromAPI($productId)
    {
        return Http::withHeaders($this->getHeaders())
                        ->get(config('services.products.url'), [
                            'usItemId' => $productId
        ]);
    }

}

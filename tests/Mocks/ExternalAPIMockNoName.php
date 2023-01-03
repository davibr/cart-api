<?php

namespace Tests\Mocks;

use App\Interfaces\ExternalAPIInterface;
use ArrayAccess;

class ExternalAPIMockNoName implements ExternalAPIInterface
{

    /**
     * Mocks the external API getting the data for the product
     *
     * @param string $productId
     * @return ArrayAccess
     */
    public function getDataFromAPI($productId)
    {
        return [
            'data' => [
                'product' => [
                    'priceInfo' => [
                        'currentPrice' => [
                            'price' => 9.99,
                        ]
                    ]
                ]
            ]
        ];
    }

}

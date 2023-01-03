<?php

namespace Tests\Mocks;

use App\Interfaces\ExternalAPIInterface;
use ArrayAccess;

class ExternalAPIMockNotFound implements ExternalAPIInterface
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
            'errors' => [
                ['error' => 'test', 'message' => '404'],
                ['error' => 'another test', 'message' => 'generic error'],
            ]
        ];
    }

}

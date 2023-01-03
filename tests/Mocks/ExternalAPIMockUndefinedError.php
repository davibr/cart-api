<?php

namespace Tests\Mocks;

use App\Interfaces\ExternalAPIInterface;
use ArrayAccess;

class ExternalAPIMockUndefinedError implements ExternalAPIInterface
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
                ['error' => 'test', 'message' => 'generic error'],
                ['error' => 'another test', 'message' => 'another generic error'],
            ]
        ];
    }

}

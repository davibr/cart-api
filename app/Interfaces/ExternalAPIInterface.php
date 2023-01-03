<?php

namespace App\Interfaces;

interface ExternalAPIInterface
{

    /**
     * Gets details from the external API about the product
     *
     * @param string $productId
     */
    public function getDataFromAPI($productId);
}

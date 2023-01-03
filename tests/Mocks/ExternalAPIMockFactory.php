<?php

namespace Tests\Mocks;

use Exception;

class ExternalAPIMockFactory
{

    static function getMock(ExternalAPIMockType $mockType)
    {
        switch ($mockType)
        {
            case ExternalAPIMockType::Functional: return new ExternalAPIMockFunctional();
            case ExternalAPIMockType::NotFound: return new ExternalAPIMockNotFound();
            case ExternalAPIMockType::UnexpectedError: return new ExternalAPIMockUndefinedError();
            case ExternalAPIMockType::NoName: return new ExternalAPIMockNoName();
            case ExternalAPIMockType::NoPrice: return new ExternalAPIMockNoPrice();
            default:
                throw new Exception('Mock type not found');
        }
    }

}

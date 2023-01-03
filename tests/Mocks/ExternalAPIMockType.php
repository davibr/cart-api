<?php

namespace Tests\Mocks;

enum ExternalAPIMockType
{

    case Functional;
    case NotFound;
    case UnexpectedError;
    case NoPrice;
    case NoName;

}

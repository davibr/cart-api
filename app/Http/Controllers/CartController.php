<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemToCartRequest;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\CartResource;
use App\Interfaces\ExternalAPIInterface;
use App\Models\User;
use App\Services\CartService;
use App\Services\UserService;
use Exception;
use function app;
use function response;

class CartController extends Controller
{

    private $cartService;

    public function __construct(ExternalAPIInterface $externalAPIService)
    {
        $this->cartService = app(CartService::class, compact('externalAPIService'));
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @return CartResource
     */
    public function show(User $user)
    {
        return new CartResource($this->cartService->getActiveCart($user));
    }

    /**
     * Add an Item to the current cart.
     *
     * @param  AddItemToCartRequest  $request
     * @return CartResource
     */
    public function addItem(AddItemToCartRequest $request)
    {
        try
        {
            $user = UserService::getUserFromRequest($request);
            return new CartResource($this->cartService->addItemToCart($request, $user));
        }
        catch (Exception $e)
        {
            return response()->json([
                        'error' => $e->getMessage(),
                            ], $e->getCode());
        }
    }

    /**
     * Add an Item to the current cart.
     *
     * @param  CheckoutRequest  $request
     * @return CartResource
     */
    public function checkout(CheckoutRequest $request)
    {
        try
        {
            $user = UserService::getUserFromRequest($request);
            return new CartResource($this->cartService->checkout($user));
        }
        catch (Exception $e)
        {
            return response()->json([
                        'error' => $e->getMessage(),
                            ], $e->getCode());
        }
    }

}

<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;

class CartService
{
    private $cart;
    private $itemService;

    public function __construct()
    {
        $this->itemService = app(ItemService::class);
    }

    /**
     * Gets a user's current active cart.
     *
     * @param  User  $user
     * @return Cart
     */
    public function getActiveCart(User $user)
    {
        $this->cart = $user->carts()->active()->first();

        if (!$this->cart) 
        {
            $this->cart = $user->carts()->create();
        }

        return $this->cart;
    }

    /**
     * Add an item to the current cart or just increments the quantity of the product.
     *
     * @param  string   $productId
     * @param  integer  $quantity
     * @return Item
     */
    private function addOrIncrementToItems($productId, $quantity)
    {
        $item = $this->cart->items()->withProduct($productId)->first();
        
        if ($item)
        {
            $item->quantity += $quantity;
            $item->save();
            return $item;
        }

        return $this->cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity
        ]);

    }

    /**
     * Add an item to the current cart.
     *
     * @param  Request  $request
     * @param  User  $user
     * @return Cart
     */
    public function addItemToCart(Request $request, User $user)
    {
        $this->getActiveCart($user);

        $item = $this->addOrIncrementToItems($request->product_id, $request->quantity);

        try 
        {
            $this->itemService->updatesItemFromAPI($item);
        }
        catch(Exception $e)
        {
            $item->delete();
            throw $e;
        }

        $this->cart->refresh();

        return $this->cart;
    }

    /**
     * Checks if the cart is ready for checkout
     * @throws Exception if cart doesn't have items
     */
    private function validatesCartForCheckout() 
    {
        if ($this->cart->items->count() == 0) 
        {
            throw new Exception("You can't checkout without items.", 400);
        }
    }

    /**
     * Checks out the user's current cart.
     *
     * @param  User  $user
     * @return Cart
     */
    public function checkout(User $user)
    {
        $this->getActiveCart($user);

        $this->validatesCartForCheckout();

        $this->cart->closed_at = now();
        $this->cart->save();

        return $this->cart;
    }


}
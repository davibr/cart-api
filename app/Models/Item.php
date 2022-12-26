<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';

    protected $fillable = ['product_id', 'quantity']; 


    /******************* 
     * 
     * Relationships
     * 
    *******************/    

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /******************* 
     * 
     * Scopes
     * 
    *******************/ 

    /**
     * Scope a query to bring the desired item.
     *
     * @param  Builder  $query
     * @param  string $product_id
     * @return Builder
     */
    public function scopeWithProduct($query, $product_id)
    {
        return $query->where('product_id', $product_id);
    }
    
    /******************* 
     * 
     * Attributes
     * 
    *******************/ 
    
    /**
     * Get the item total price.
     *
     * @return Attribute
     */
    protected function total(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['quantity'] * $attributes['individual_price'],
        );
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddItemToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'numeric|required|exists:users,id',
            'product_id' => 'required|max:255',
            'quantity' => 'numeric|integer|required|min:1'
        ];
    }
}

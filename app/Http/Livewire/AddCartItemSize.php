<?php

namespace App\Http\Livewire;

use App\Models\Size;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class AddCartItemSize extends Component
{
    public $product, $sizes;
    public $size_id = '', $color_id = '';
    public $colors = [], $options = [];
    public $qty = 1;
    public $quantity = 0;

    public function mount()
    {
        $this->sizes = $this->product->sizes;
        $this->options['image'] = Storage::url($this->product->images->first()->url);
    }

    public function updatedSizeId($value)
    {
        $size = Size::find($value);
        $this->colors = $size->colors;
        $this->options['size'] = $size->name;
        $this->options['sizeId'] = $size->id;
    }

    public function updatedColorId($value)
    {
        $size = Size::find($this->size_id);
        $color = $size->colors->find($value);
        $this->quantity = qtyAvailable($this->product->id, $this->color_id, $this->size_id);
        $this->options['color'] = $color->name;
        $this->options['colorId'] = $color->id;

    }

    public function decrement()
    {
        $this->qty--;
    }

    public function increment()
    {
        $this->qty++;
    }

    public function checkQty($qty)
    {
        if ($qty >= qtyAvailable($this->product->id, $this->color_id, $this->size_id)) {
            return qtyAvailable($this->product->id, $this->color_id, $this->size_id);
        } else {
            return $qty;
        }
    }

    public function addItem()
    {
        Cart::add([
            'id' => $this->product->id,
            'name' => $this->product->name,
            'qty' => $this->checkQty($this->qty),
            'price' => $this->product->price,
            'weight' => 550,
            'options' => $this->options
        ]);

        $this->quantity = qtyAvailable($this->product->id, $this->color_id, $this->size_id);

        $this->reset('qty');

        $this->emitTo('dropdown-cart', 'render');
    }

    public function render()
    {
        return view('livewire.add-cart-item-size');
    }
}

<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;

class OrderObserver
{
    public function created(Order $order): void
    {
        $order_items_total_price = OrderItem::where('order_id', $order->id)->sum('total_price');
        $order->total_price = $order->shipping_price + $order_items_total_price;
        $order->saveQuietly();

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(Order $order): void
    {
        $order_items_total_price = OrderItem::where('order_id', $order->id)->sum('total_price');
        $order->total_price = $order->shipping_price + $order_items_total_price;
        $order->saveQuietly();
    }
}

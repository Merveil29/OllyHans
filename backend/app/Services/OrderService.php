<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Stock;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function getAll(array $filters = [])
    {
        $query = Order::with(['items'])->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function create(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            $data['order_number'] = $data['order_number'] ?? 'CMD-' . strtoupper(Str::random(8));

            $order = Order::create($data);

            $total = 0;
            foreach ($items as $item) {
                $variant = ProductVariant::with('stock')->findOrFail($item['product_variant_id']);

                if (!$variant->stock || $variant->stock->quantity < ($item['quantity'] ?? 1)) {
                    throw new Exception("Stock insuffisant pour {$variant->name}");
                }

                $unitPrice = $item['unit_price'] ?? $variant->price;
                $lineTotal = $unitPrice * ($item['quantity'] ?? 1);
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ]);

                // Decrement stock
                $variant->stock->decrement('quantity', $item['quantity'] ?? 1);
            }

            $order->update([
                'subtotal' => $total,
                'total' => $total + ($data['shipping_cost'] ?? 0) - ($data['discount'] ?? 0),
            ]);

            return $order->fresh(['items']);
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh();
    }

    public function updatePaymentStatus(Order $order, string $paymentStatus): Order
    {
        $order->update(['payment_status' => $paymentStatus]);
        return $order->fresh();
    }
}

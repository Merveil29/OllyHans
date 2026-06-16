<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminOrderController extends Controller
{
    use ApiResponse;
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'payment_status', 'per_page']);
            $orders = $this->orderService->getAll($filters);

            return $this->paginated($orders, function ($order) {
                return (new OrderResource($order))->toArray(request());
            });
        } catch (Exception $e) {
            Log::error('Erreur récupération commandes: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la récupération des commandes.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = Order::with(['items', 'client'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
            ]);
        } catch (Exception $e) {
            return $this->notFound('Commande non trouvée.');
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled']);
            $order = Order::findOrFail($id);
            $order = $this->orderService->updateStatus($order, $request->input('status'));
            return response()->json([
                'success' => true,
                'message' => 'Statut de la commande mis à jour.',
                'data' => new OrderResource($order),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour statut commande: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour du statut.');
        }
    }

    public function updatePayment(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['payment_status' => 'required|string|in:pending,paid,failed,refunded']);
            $order = Order::findOrFail($id);
            $order = $this->orderService->updatePaymentStatus($order, $request->input('payment_status'));
            return response()->json([
                'success' => true,
                'message' => 'Statut de paiement mis à jour.',
                'data' => new OrderResource($order),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur mise à jour paiement: ' . $e->getMessage());
            return $this->serverError($e, 'Erreur lors de la mise à jour du paiement.');
        }
    }
}

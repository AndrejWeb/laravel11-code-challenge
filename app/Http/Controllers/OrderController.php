<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Jobs\ProcessOrder;
use Laravel\Sanctum\PersonalAccessToken;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', request()->header('Authorization'));

        // Use Sanctum's PersonalAccessToken model to locate the token
        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken) {
            // Retrieve the associated user ID
            $userId = $accessToken->tokenable_id;
        } else {
            // Token not found or invalid
            return response()->json(['success' => false, 'message' => 'Invalid token provided'], 404);
        }

        // Validate the incoming request data
        $request->validate([
            'products' => 'required|array|min:1', // Array of products
            'products.*.id' => 'required|exists:products,id', // Product ID
            'products.*.quantity' => 'required|integer|min:1', // Quantity
        ]);

        // Collect all product IDs from the request
        $productIds = collect($request->products)->pluck('id');

        // Fetch all products at once using the collected IDs
        $products = Product::whereIn('id', $productIds)->get();

        // Calculate order total based on product IDs and quantities
        $orderTotal = 0;
        foreach ($request->products as $productData) {
            // Find the product from the fetched products
            $product = $products->firstWhere('id', $productData['id']);
            if ($product) {
                $orderTotal += $product->price * $productData['quantity'];
            }
        }

        // Check if order total is less than 15 euros
        if ($orderTotal < 15) {
            return response()->json(['success' => false, 'message' => 'Order total must be at least 15 euros'], 422);
        }

        // Create the order
        $order = Order::create([
            'user_id' => $userId,
            'total' => $orderTotal,
        ]);

        // Attach products to the order
        foreach ($request->products as $productData) {
            $order->products()->attach($productData['id'], ['quantity' => $productData['quantity']]);
        }

        // Dispatch ProcessOrder job with 3 minutes delay after order creation
        ProcessOrder::dispatch($order)->delay(now()->addMinutes(3));

        return response()->json(['success' => true, 'message' => 'Order created successfully', 'order' => $order], 201);
    }
}

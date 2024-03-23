<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

it('creates an order successfully', function () {
$request = new Request([
'products' => [
['id' => 1, 'quantity' => 2],
['id' => 2, 'quantity' => 3],
],
]);

$response = (new \App\Http\Controllers\OrderController())->create($request);

expect($response)->toBeInstanceOf(JsonResponse::class);
expect($response->getStatusCode())->toBe(201);

$content = $response->getData(true);
expect($content)->toHaveKey('message');
expect($content['message'])->toBe('Order created successfully');
expect($content)->toHaveKey('order');
expect($content['order'])->toBeInstanceOf(Order::class);
});

it('returns an error if products array is empty', function () {
$request = new Request([
'products' => [],
]);

$response = (new \App\Http\Controllers\OrderController())->create($request);

expect($response)->toBeInstanceOf(JsonResponse::class);
expect($response->getStatusCode())->toBe(422);

$content = $response->getData(true);
expect($content)->toHaveKey('error');
expect($content['error'])->toBe('Products array must not be empty');
});

it('returns an error if product ID does not exist', function () {
$request = new Request([
'products' => [
['id' => 100, 'quantity' => 2],
],
]);

$response = (new \App\Http\Controllers\OrderController())->create($request);

expect($response)->toBeInstanceOf(JsonResponse::class);
expect($response->getStatusCode())->toBe(422);

$content = $response->getData(true);
expect($content)->toHaveKey('error');
expect($content['error'])->toBe('Product ID 100 does not exist');
});

it('returns an error if order total is less than 15 euros', function () {
Product::factory()->create(['price' => 5]);

$request = new Request([
'products' => [
['id' => 1, 'quantity' => 1],
],
]);

$response = (new \App\Http\Controllers\OrderController())->create($request);

expect($response)->toBeInstanceOf(JsonResponse::class);
expect($response->getStatusCode())->toBe(422);

$content = $response->getData(true);
expect($content)->toHaveKey('error');
expect($content['error'])->toBe('Order total must be at least 15 euros');
});

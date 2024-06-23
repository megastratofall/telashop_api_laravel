<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
public function createPreference(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    // Obtener el carrito del usuario con los productos asociados
    $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

    // Preparar los ítems para la preferencia de pago
    $items = [];
    foreach ($cartItems as $cartItem) {
        $items[] = [
            'id' => $cartItem->product_id,
            'title' => $cartItem->product->name,
            'description' => $cartItem->product->description,
            'quantity' => 1,
            'currency_id' => 'ARS',
            'unit_price' => floatval($cartItem->product->price),
        ];
    }

    // Datos de la preferencia de pago
    $preferenceData = [
        'items' => $items,
        'payer' => [
            'email' => $user->email,
        ],
        'back_urls' => [
            'success' => url('http://localhost:3000/products'),
            'failure' => url('http://localhost:3000/cart'),
            'pending' => url('http://localhost:3000/cart'),
        ],
        'auto_return' => 'approved',
    ];
    try {
        // Realizar la solicitud a MercadoPago para crear la preferencia de pago
        $response = Http::withOptions(['verify' => false])
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . config('services.mercadopago.token'),
                        ])
                        ->post('https://api.mercadopago.com/checkout/preferences', $preferenceData);

        // Verificar la respuesta de MercadoPago
        if ($response->successful()) {
            $preference = $response->json();
            $initPoint = $preference['init_point'];

            if ($initPoint) {
                return response()->json(['init_point' => $initPoint]);
            } else {
                Log::error('MercadoPago API error: Init point not found in response');
                return response()->json(['error' => 'Error al crear la preferencia de pago: Punto de inicio no encontrado'], 500);
            }
        } else {
            Log::error('MercadoPago API error: ' . $response->body());
            return response()->json(['error' => 'Error al crear la preferencia de pago: ' . $response->body()], 500);
        }
    } catch (\Exception $e) {
        Log::error('Exception in createPreference: ' . $e->getMessage());
        return response()->json(['error' => 'Error al crear la preferencia de pago: ' . $e->getMessage()], 500);
    }
}


public function handlePaymentResult(Request $request)
{
    $status = $request->query('status');
    $user = Auth::user();

if ($status === 'approved') {
// Vaciar el carrito del usuario si es necesario
if ($user) {
        Cart::where('user_id', $user->id)->delete();
}
        return redirect('http://localhost:3000/products'); // Redirige a productos después de vaciar el carrito
} else {
        return redirect('http://localhost:3000/cart'); // Redirige al carrito en caso de error
}
}

}
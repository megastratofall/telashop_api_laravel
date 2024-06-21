<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPreference(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    
        // Obtener productos del carrito del usuario con la relación `product`
        $cart = $user->cart()->get();
    
        // Preparar los items para la preferencia de pago
        $items = [];
        foreach ($cart as $cartItem) {
            $items[] = [
                'id' => $cartItem->id,
                'title' => $cartItem->name,
                'description' => $cartItem->description,
                'quantity' => intval($cartItem->pivot->quantity),
                'currency_id' => 'ARS',
                'unit_price' => floatval($cartItem->price),
            ];
        }
    
        // Datos de la preferencia de pago
        $preferenceData = [
            'items' => $items,
            'payer' => [
                'email' => $user->email,
            ],
            'back_urls' => [
                'success' => route('payment.success'),
                'failure' => route('payment.failure'),
                'pending' => route('payment.pending'),
            ],
            'auto_return' => 'approved',
        ];
    
        try {
            // Hacer la solicitud a la API de MercadoPago
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.mercadopago.token'),
            ])->post('https://api.mercadopago.com/checkout/preferences', $preferenceData);
        
            if ($response->successful()) {
                $preferenceId = $response->json('id');
                if ($preferenceId) {
                    return response()->json(['preference_id' => $preferenceId]);
                } else {
                    Log::error('MercadoPago API error: Preference ID not found in response');
                    return response()->json(['error' => 'Error al crear la preferencia de pago: ID no encontrado'], 500);
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
    
    public function paymentSuccess(Request $request)
    {
        // Procesar la confirmación de pago exitoso
        return response()->json(['message' => 'Pago exitoso']);
    }

    public function paymentFailure(Request $request)
    {
        // Procesar la confirmación de pago fallido
        return response()->json(['message' => 'Pago fallido']);
    }

    public function paymentPending(Request $request)
    {
        // Procesar la confirmación de pago pendiente
        return response()->json(['message' => 'Pago pendiente']);
    }
}
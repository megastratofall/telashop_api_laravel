<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user->cart()->attach($product->id, ['quantity' => $request->quantity]);

        return response()->json(['message' => 'Producto agregado al carrito correctamente'], 201);
    }

    public function updateCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user->cart()->updateExistingPivot($request->product_id, ['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cantidad actualizada correctamente'], 200);
    }

    public function removeFromCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user->cart()->detach($request->product_id);

        return response()->json(['message' => 'Producto eliminado del carrito correctamente'], 200);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $cart = $user->cart;
        return response()->json($cart, 200);
    }
}

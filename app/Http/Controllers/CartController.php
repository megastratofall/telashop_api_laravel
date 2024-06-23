<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

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
        ]);

        $product = Product::findOrFail($request->product_id);

        // Crear una nueva entrada en el carrito
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json(['message' => 'Producto agregado al carrito correctamente', 'cart_item' => $cartItem], 201);
    }

    public function removeFromCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $request->validate([
            'cart_item_id' => 'required|exists:carts,id',
        ]);

        // Eliminar el producto específico del carrito del usuario
        Cart::where('user_id', $user->id)
            ->where('id', $request->cart_item_id)
            ->delete();

        return response()->json(['message' => 'Producto eliminado del carrito correctamente'], 200);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Obtener todos los productos en el carrito del usuario con sus detalles
        $cart = Cart::where('user_id', $user->id)
                    ->with(['product:id,name,price'])
                    ->get();

        return response()->json($cart, 200);
    }
}
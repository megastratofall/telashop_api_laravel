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
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Crear una nueva entrada en el carrito
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Producto agregado al carrito correctamente', 'cart_item' => $cartItem], 201);
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

        // Buscar el producto en el carrito del usuario y actualizar la cantidad
        $cartItem = Cart::where('user_id', $user->id)
                        ->where('product_id', $request->product_id)
                        ->firstOrFail();

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cantidad actualizada correctamente', 'cart_item' => $cartItem], 200);
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

        // Eliminar el producto del carrito del usuario
        Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
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
                    ->with(['product:id,name,price']) // Incluir solo los campos necesarios del producto
                    ->get();

        return response()->json($cart, 200);
    }

}

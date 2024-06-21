<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $products = [
            ['name' => 'Algodón', 'description' => 'Tela de algodón de alta calidad, suave y transpirable.', 'price' => 500.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Algod%C3%B3n'],
            ['name' => 'Lino', 'description' => 'Tela de lino, fresca y duradera, ideal para climas cálidos.', 'price' => 700.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Lino'],
            ['name' => 'Seda', 'description' => 'Tela de seda natural, suave y brillante, perfecta para prendas elegantes.', 'price' => 1500.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Seda'],
            ['name' => 'Lana', 'description' => 'Tela de lana, cálida y confortable, ideal para el invierno.', 'price' => 1200.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Lana'],
            ['name' => 'Poliéster', 'description' => 'Tela de poliéster, resistente y de fácil cuidado.', 'price' => 400.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Poli%C3%A9ster'],
            ['name' => 'Gasa', 'description' => 'Tela de gasa, ligera y vaporosa, perfecta para prendas veraniegas.', 'price' => 600.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Gasa'],
            ['name' => 'Satén', 'description' => 'Tela de satén, suave y con un acabado brillante, ideal para ropa de noche.', 'price' => 800.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Sat%C3%A9n'],
            ['name' => 'Denim', 'description' => 'Tela de denim, resistente y versátil, perfecta para jeans.', 'price' => 900.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Denim'],
            ['name' => 'Franela', 'description' => 'Tela de franela, suave y cálida, ideal para camisas y pijamas.', 'price' => 750.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Franela'],
            ['name' => 'Terciopelo', 'description' => 'Tela de terciopelo, suave y lujosa, perfecta para ropa y decoración.', 'price' => 1000.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Terciopelo'],
            ['name' => 'Tul', 'description' => 'Tela de tul, ligera y transparente, ideal para vestidos de novia y tutús.', 'price' => 650.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Tul'],
            ['name' => 'Encaje', 'description' => 'Tela de encaje, delicada y elegante, perfecta para ropa interior y vestidos.', 'price' => 1300.00, 'image' => 'https://via.placeholder.com/640x480.png?text=Encaje'],
        ];

        static $index = 0;

        $product = $products[$index++ % count($products)];

        return [
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'],
            'image' => $product['image'],
        ];
    }
}

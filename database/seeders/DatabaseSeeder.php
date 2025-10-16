<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Kedai Holand satu',
            'email' => 'adminkedaiholandsatu@gmail.com',
            'password' => 'kedaiholand123',
        ]);

        $categories = collect([
            ['name' => 'Makanan', 'description' => 'Aneka makanan utama'],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan hangat'],
            ['name' => 'Dessert', 'description' => 'Makanan penutup dan camilan'],
        ])->map(function ($data) {
            return Category::create([
                'id' => Str::uuid(),
                ...$data
            ]);
        });

        $products = collect([
            ['Nasi Goreng Spesial', 25000, 'Makanan'],
            ['Mie Goreng Jawa', 22000, 'Makanan'],
            ['Ayam Bakar Madu', 28000, 'Makanan'],
            ['Sate Ayam', 27000, 'Makanan'],
            ['Nasi Ayam Geprek', 23000, 'Makanan'],
            ['Nasi Campur Bali', 26000, 'Makanan'],
            ['Nasi Rendang Padang', 29000, 'Makanan'],
            ['Soto Ayam Lamongan', 24000, 'Makanan'],
            ['Bakso Urat Pedas', 20000, 'Makanan'],
            ['Mie Ayam Pangsit', 22000, 'Makanan'],
            ['Lontong Sayur Medan', 20000, 'Makanan'],
            ['Nasi Goreng Seafood', 27000, 'Makanan'],
            ['Ayam Penyet Sambal Ijo', 25000, 'Makanan'],
            ['Nasi Goreng Kampung', 23000, 'Makanan'],
            ['Spaghetti Bolognese', 28000, 'Makanan'],
            ['Chicken Katsu Rice', 27000, 'Makanan'],
            ['Steak Sapi Lada Hitam', 35000, 'Makanan'],

            ['Es Teh Manis', 8000, 'Minuman'],
            ['Es Jeruk Segar', 9000, 'Minuman'],
            ['Kopi Hitam Tubruk', 10000, 'Minuman'],
            ['Cappuccino', 18000, 'Minuman'],
            ['Lemon Tea Dingin', 10000, 'Minuman'],
            ['Smoothie Mangga', 15000, 'Minuman'],
            ['Milkshake Coklat', 16000, 'Minuman'],
            ['Milkshake Stroberi', 16000, 'Minuman'],
            ['Jus Alpukat', 17000, 'Minuman'],
            ['Jus Jambu Merah', 15000, 'Minuman'],
            ['Air Mineral', 6000, 'Minuman'],
            ['Kopi Latte', 18000, 'Minuman'],
            ['Es Kopi Susu Gula Aren', 20000, 'Minuman'],
            ['Matcha Latte', 19000, 'Minuman'],
            ['Teh Tarik Panas', 12000, 'Minuman'],
            ['Coklat Panas', 13000, 'Minuman'],
            ['Americano', 16000, 'Minuman'],

            ['Brownies Coklat', 15000, 'Dessert'],
            ['Pudding Coklat', 12000, 'Dessert'],
            ['Cheesecake', 20000, 'Dessert'],
            ['Es Krim Vanila', 10000, 'Dessert'],
            ['Pancake Strawberry', 18000, 'Dessert'],
            ['Roti Bakar Keju', 13000, 'Dessert'],
            ['Roti Bakar Coklat Pisang', 14000, 'Dessert'],
            ['Waffle Coklat', 17000, 'Dessert'],
            ['Tiramisu Slice', 20000, 'Dessert'],
            ['Donat Glaze Coklat', 10000, 'Dessert'],
            ['Croissant Mentega', 13000, 'Dessert'],
            ['Kue Cubit Matcha', 12000, 'Dessert'],
            ['Banana Split', 18000, 'Dessert'],
            ['Red Velvet Cake', 21000, 'Dessert'],
            ['Macaron Mix', 22000, 'Dessert'],
            ['Pudding Mangga', 12000, 'Dessert'],
        ])->map(function ($data) use ($categories) {
            $category = $categories->firstWhere('name', $data[2]);
            return Product::create([
                'id' => Str::uuid(),
                'category_id' => $category->id,
                'name' => $data[0],
                'description' => fake()->sentence(),
                'price' => $data[1],
                'is_available' => true,
            ]);
        });

        for ($i = 1; $i <= 5; $i++) {
            $order = Order::create([
                'id' => Str::uuid(),
                'order_code' => 'ORD-' . strtoupper(Str::random(6)),
                'name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'table_number' => 'T' . rand(1, 10),
                'total_price' => 0,
                'payment_method' => fake()->randomElement(['cash', 'transfer']),
                'status' => 'menunggu',
                'completed_at' => null,
            ]);

            $itemCount = rand(3, 5);
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $qty = rand(1, 3);
                $subtotal = $product->price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'id' => Str::uuid(),
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'note' => fake()->optional()->sentence(),
                ]);
            }

            $order->update(['total_price' => $total]);

            OrderLog::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'status' => 'menunggu',
                'message' => 'Pesanan baru dibuat dan menunggu diproses.',
            ]);
        }

    }
}

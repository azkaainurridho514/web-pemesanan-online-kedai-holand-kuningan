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
         // === CATEGORY SEEDER ===
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

        // === PRODUCT SEEDER ===
        $products = collect([
            ['Nasi Goreng Spesial', 25000, 'Makanan'],
            ['Mie Goreng', 20000, 'Makanan'],
            ['Ayam Bakar', 28000, 'Makanan'],
            ['Sate Ayam', 27000, 'Makanan'],
            ['Nasi Ayam Geprek', 23000, 'Makanan'],
            ['Es Teh Manis', 8000, 'Minuman'],
            ['Es Jeruk', 9000, 'Minuman'],
            ['Kopi Hitam', 10000, 'Minuman'],
            ['Cappuccino', 18000, 'Minuman'],
            ['Lemon Tea', 10000, 'Minuman'],
            ['Brownies Coklat', 15000, 'Dessert'],
            ['Pudding Coklat', 12000, 'Dessert'],
            ['Cheesecake', 20000, 'Dessert'],
            ['Es Krim Vanila', 10000, 'Dessert'],
            ['Pancake Strawberry', 18000, 'Dessert'],
            ['Smoothie Mangga', 15000, 'Minuman'],
            ['Roti Bakar Keju', 13000, 'Dessert'],
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

        // === ORDER SEEDER ===
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

            // update total
            $order->update(['total_price' => $total]);

            // log awal
            OrderLog::create([
                'id' => Str::uuid(),
                'order_id' => $order->id,
                'status' => 'menunggu',
                'message' => 'Pesanan baru dibuat dan menunggu diproses.',
            ]);
        }
    }
}

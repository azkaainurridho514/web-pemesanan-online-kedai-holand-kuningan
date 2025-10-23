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
use App\Models\Option;
use App\Models\OptionItems;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // 👤 Admin Default
        User::factory()->create([
            'name' => 'Admin Kedai Holand satu',
            'email' => 'adminkedaiholandsatu@gmail.com',
            'password' => 'kedaiholand123',
        ]);

        // 🍱 Kategori
        $categories = collect([
            ['name' => 'Makanan', 'description' => 'Aneka makanan utama'],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan hangat'],
            ['name' => 'Dessert', 'description' => 'Makanan penutup dan camilan'],
        ])->map(fn($data) => Category::create([
            'id' => Str::uuid(),
            ...$data
        ]));

        // 🧊 Opsi Suhu (dipakai hanya untuk beberapa minuman)
        $optionTemperature = Option::create([
            'id' => Str::uuid(),
            'name' => 'Suhu Minuman',
        ]);

        $optionItemsTemperature = collect(['Dingin', 'Panas', 'Normal'])->map(fn($item) =>
            OptionItems::create([
                'id' => Str::uuid(),
                'option_id' => $optionTemperature->id,
                'name' => $item,
            ])
        );

        // 🍔 Produk (lengkap seperti seeder kamu sebelumnya)
        $products = collect([
            // === MAKANAN ===
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

            // === MINUMAN ===
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

            // === DESSERT ===
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
        ])->map(function ($data) use ($categories, $optionTemperature) {
            $category = $categories->firstWhere('name', $data[2]);
            $optionId = null;

            // produk yang punya opsi suhu
            $withOption = [
                'Es Teh Manis', 'Es Jeruk Segar', 'Kopi Hitam Tubruk',
                'Cappuccino', 'Lemon Tea Dingin', 'Air Mineral',
                'Kopi Latte', 'Es Kopi Susu Gula Aren', 'Matcha Latte',
                'Teh Tarik Panas', 'Coklat Panas', 'Americano'
            ];

            if (in_array($data[0], $withOption)) {
                $optionId = $optionTemperature->id;
            }

            return Product::create([
                'id' => Str::uuid(),
                'category_id' => $category->id,
                'option_id' => $optionId, // boleh null
                'name' => $data[0],
                'description' => fake()->sentence(),
                'price' => $data[1],
                'is_available' => true,
            ]);
        });

        // 🧾 Pesanan Dummy
        // for ($i = 1; $i <= 5; $i++) {
        //     $order = Order::create([
        //         'id' => Str::uuid(),
        //         'order_code' => 'ORD-' . strtoupper(Str::random(6)),
        //         'name' => fake()->name(),
        //         'phone' => fake()->phoneNumber(),
        //         'table_number' => 'T' . rand(1, 10),
        //         'total_price' => 0,
        //         'payment_method' => fake()->randomElement(['cash', 'transfer']),
        //         'status' => 'menunggu',
        //         'completed_at' => null,
        //     ]);

        //     $itemCount = rand(3, 5);
        //     $total = 0;

        //     for ($j = 0; $j < $itemCount; $j++) {
        //         $product = $products->random();
        //         $qty = rand(1, 3);
        //         $subtotal = $product->price * $qty;
        //         $total += $subtotal;

        //         OrderItem::create([
        //             'id' => Str::uuid(),
        //             'order_id' => $order->id,
        //             'product_id' => $product->id,
        //             'quantity' => $qty,
        //             'subtotal' => $subtotal,
        //             'note' => fake()->optional()->sentence(),
        //         ]);
        //     }

        //     $order->update(['total_price' => $total]);

        //     OrderLog::create([
        //         'id' => Str::uuid(),
        //         'order_id' => $order->id,
        //         'status' => 'menunggu',
        //         'message' => 'Pesanan baru dibuat dan menunggu diproses.',
        //     ]);
        // }
    }
    // public function order(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'  => 'required|string|max:100',
    //         'phone' => 'required|string|max:20',
    //     ]);

    //     $cart = json_decode($request->cookie($this->cookieName), true) ?? [];

    //     if (empty($cart)) {
    //         return response()->json(['message' => 'Keranjang kosong'], 400);
    //     }

    //     $itemsToOrder = array_filter($cart, fn($item) => empty($item['is_order']) || $item['is_order'] === false);

    //     if (empty($itemsToOrder)) {
    //         return response()->json(['message' => 'Tidak ada item baru untuk dipesan'], 400);
    //     }

    //     foreach ($itemsToOrder as $item) {
    //         \DB::table('orders')->insert([
    //             'name'       => $validated['name'],
    //             'phone'      => $validated['phone'],
    //             'product_id' => $item['product_id'],
    //             'nama'       => $item['nama'],
    //             'qty'        => $item['qty'],
    //             'harga'      => $item['harga'],
    //             'desc'       => $item['desc'] ?? '',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     foreach ($cart as &$item) {
    //         if (in_array($item, $itemsToOrder)) {
    //             $item['is_order'] = true;
    //         }
    //     }

    //     $cookie = cookie($this->cookieName, json_encode($cart), $this->cookieTime);

    //     return response()->json([
    //         'message' => 'Pesanan berhasil disimpan!',
    //         'ordered_count' => count($itemsToOrder),
    //     ])->cookie($cookie);
    // }

}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
        User::factory()->create([
            'name' => 'Admin Kedai Holand',
            'email' => 'adminkedaiholand@gmail.com',
            'password' => 'kedaiholand123',
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        Option::truncate();
        OptionItems::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = collect([
            ['name' => 'Makanan', 'description' => 'Aneka makanan utama dan lauk.'],
            ['name' => 'Minuman', 'description' => 'Minuman segar dan panas.'],
            ['name' => 'Dessert', 'description' => 'Makanan penutup manis.'],
            ['name' => 'Snack', 'description' => 'Cemilan ringan untuk santai.'],
            ['name' => 'Kopi', 'description' => 'Berbagai racikan kopi.'],
        ])->map(fn($data) => Category::create([
            'id' => Str::uuid(),
            ...$data
        ]));

        $optionSets = [
            'Makanan' => [
                'Level Pedas' => ['Tidak Pedas', 'Sedang', 'Pedas', 'Super Pedas'],
                'Ukuran Porsi' => ['Kecil', 'Sedang', 'Besar'],
            ],
            'Minuman' => [
                'Suhu' => ['Dingin', 'Normal', 'Panas'],
                'Gula' => ['Tanpa Gula', 'Sedikit Gula', 'Manis'],
            ],
            'Dessert' => [
                'Topping' => ['Coklat', 'Keju', 'Stroberi', 'Kacang'],
                'Ukuran' => ['Mini', 'Reguler'],
            ],
            'Snack' => [
                'Rasa' => ['Asin', 'Pedas', 'Barbeque', 'Keju'],
                'Kemasan' => ['Kecil', 'Sedang', 'Besar'],
            ],
            'Kopi' => [
                'Kafein' => ['Normal', 'Decaf'],
                'Gula' => ['Tanpa Gula', 'Manis'],
                'Suhu' => ['Panas', 'Dingin'],
            ],
        ];

        $options = collect();
        foreach ($optionSets as $categoryName => $opts) {
            foreach ($opts as $optName => $items) {
                $option = Option::create([
                    'id' => Str::uuid(),
                    'name' => $optName,
                ]);

                foreach ($items as $item) {
                    OptionItems::create([
                        'id' => Str::uuid(),
                        'option_id' => $option->id,
                        'name' => $item,
                    ]);
                }

                $options->push([
                    'category' => $categoryName,
                    'option' => $option,
                ]);
            }
        }

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
            ['Spaghetti Bolognese', 28000, 'Makanan'],
            ['Chicken Katsu Rice', 27000, 'Makanan'],
            ['Steak Sapi Lada Hitam', 35000, 'Makanan'],
            ['Rawon Daging Sapi', 30000, 'Makanan'],
            ['Gudeg Jogja Komplit', 32000, 'Makanan'],
            ['Nasi Liwet Solo', 27000, 'Makanan'],
            ['Ayam Betutu Bali', 33000, 'Makanan'],
            ['Tongseng Kambing', 34000, 'Makanan'],
            ['Ikan Bakar Jimbaran', 36000, 'Makanan'],

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
            ['Es Kopi Susu Gula Aren', 20000, 'Minuman'],
            ['Matcha Latte', 19000, 'Minuman'],
            ['Teh Tarik Panas', 12000, 'Minuman'],
            ['Coklat Panas', 13000, 'Minuman'],
            ['Americano', 16000, 'Minuman'],
            ['Es Kelapa Muda', 12000, 'Minuman'],
            ['Es Lemon Mint', 13000, 'Minuman'],
            ['Jus Nanas Segar', 14000, 'Minuman'],
            ['Es Milo Dingin', 15000, 'Minuman'],

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
            ['Crepe Coklat Pisang', 16000, 'Dessert'],
            ['Cupcake Vanilla', 13000, 'Dessert'],
            ['Donat Keju Lumer', 11000, 'Dessert'],
            ['Lava Cake Coklat', 19000, 'Dessert'],

            // === SNACK ===
            ['Keripik Singkong Balado', 10000, 'Snack'],
            ['Kentang Goreng', 15000, 'Snack'],
            ['Tahu Crispy', 12000, 'Snack'],
            ['Tempe Mendoan', 10000, 'Snack'],
            ['Cireng Isi Ayam', 12000, 'Snack'],
            ['Risol Mayo', 13000, 'Snack'],
            ['Sosis Goreng', 14000, 'Snack'],
            ['Popcorn Caramel', 10000, 'Snack'],
            ['Pisang Goreng Keju', 14000, 'Snack'],
            ['Onion Ring', 15000, 'Snack'],
            ['Keripik Kentang Asin', 9000, 'Snack'],
            ['Donat Mini', 10000, 'Snack'],
            ['Martabak Mini', 12000, 'Snack'],
            ['Tahu Walik Pedas', 13000, 'Snack'],
            ['Roti Sobek Coklat', 13000, 'Snack'],
            ['Keripik Pisang Coklat', 11000, 'Snack'],
            ['Bakwan Sayur Renyah', 10000, 'Snack'],
            ['Sempol Ayam', 12000, 'Snack'],
            ['Kentang Spiral', 14000, 'Snack'],
            ['Singkong Keju', 13000, 'Snack'],

            // === KOPI ===
            ['Espresso', 15000, 'Kopi'],
            ['Latte', 18000, 'Kopi'],
            ['Cappuccino Kopi', 18000, 'Kopi'],
            ['Americano Kopi', 16000, 'Kopi'],
            ['Kopi Susu Aren', 20000, 'Kopi'],
            ['Mocca Latte', 19000, 'Kopi'],
            ['Caramel Macchiato', 21000, 'Kopi'],
            ['Cold Brew', 22000, 'Kopi'],
            ['Kopi Vietnam Drip', 20000, 'Kopi'],
            ['Affogato', 23000, 'Kopi'],
            ['Kopi Tubruk Bali', 16000, 'Kopi'],
            ['Flat White', 18000, 'Kopi'],
            ['Piccolo Latte', 17000, 'Kopi'],
            ['Irish Coffee', 24000, 'Kopi'],
            ['Hazelnut Latte', 20000, 'Kopi'],
            ['Vanilla Latte', 19000, 'Kopi'],
            ['Long Black', 17000, 'Kopi'],
            ['Cortado', 18000, 'Kopi'],
            ['Kopi Luwak Premium', 50000, 'Kopi'],
            ['Frappuccino', 22000, 'Kopi'],
        ]);

        $products = $products->shuffle();

        $noOptionProducts = $products->random(22);

        foreach ($products as $data) {
            $category = $categories->firstWhere('name', $data[2]);
            $option_id = $noOptionProducts->contains($data)
                ? null
                : $options->where('category', $data[2])->pluck('option')->random()->id;

            Product::create([
                'id' => Str::uuid(),
                'category_id' => $category->id,
                'option_id' => $option_id,
                'name' => $data[0],
                'description' => fake()->sentence(),
                'price' => $data[1],
                'is_available' => true,
            ]);
        }

        Product::inRandomOrder()->limit(25)->update(['is_available' => false]);

        echo "Seeder sukses: urutan produk diacak, 22 tanpa opsi, 25 non-available ✅\n";


    }
}

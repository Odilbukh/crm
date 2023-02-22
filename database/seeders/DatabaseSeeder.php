<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Filament\Resources\OrderResource;
use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    const IMAGE_URL = 'https://source.unsplash.com/random/200x200/?img=1';

    public function run(): void
    {
        // Clear images
        Storage::deleteDirectory('public');

        // Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ],
        [   'name' => 'Admin User',
            'password' => Hash::make('12345678')
        ]);

        $this->command->info('Admin user created.');

//        $user = User::firstOrCreate(
//            ['email' => 'user@example.com'],
//            ['name' => 'User',
//            'password' => Hash::make('12345678')]
//        );



        $this->call([
            ShieldSeeder::class
        ]);

        // Shop
        $categories = Category::factory()->count(20)
            ->has(
                Category::factory()->count(3),
                'children'
            )->create();
        $this->command->info('Shop categories created.');

        $brands = Brand::factory()->count(20)
            ->create();
        $this->command->info('Shop brands created.');

        $customers = Customer::factory()->count(1000)
            ->create();
        $this->command->info('Shop customers created.');

        $products = Product::factory()->count(50)
            ->sequence(fn ($sequence) => ['brand_id' => $brands->random(1)->first()->id])
            ->hasAttached($categories->random(rand(3, 6)), ['created_at' => now(), 'updated_at' => now()])
//            ->has(
//                Comment::factory()->count(rand(10, 20))
//                    ->state(fn (array $attributes, Product $product) => ['customer_id' => $customers->random(1)->first()->id]),
//            )
            ->create();
        $this->command->info('Shop products created.');

        $orders = Order::factory()->count(1000)
            ->sequence(fn ($sequence) => ['customer_id' => $customers->random(1)->first()->id])
            ->has(Payment::factory()->count(rand(1, 3)))
            ->has(
                OrderItem::factory()->count(rand(2, 5))
                    ->state(fn (array $attributes, Order $order) => ['product_id' => $products->random(1)->first()->id]),
                'items'
            )
            ->create();

//        foreach ($orders->random(rand(5, 8)) as $order) {
//            Notification::make()
//                ->title('New order')
//                ->icon('heroicon-o-shopping-bag')
//                ->body("**{$order->customer->name} ordered {$order->items->count()} products.**")
//                ->actions([
//                    Action::make('View')
//                        ->url(OrderResource::getUrl('edit', ['record' => $order])),
//                ])
//                ->sendToDatabase($user);
//        }
        $this->command->info('Shop orders created.');
    }
}

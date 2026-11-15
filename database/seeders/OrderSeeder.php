<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        User::has('client')->with('client.defaultAddress')->get()->each(function (User $user) use ($products) {
            $address = $user->client->defaultAddress;

            Order::factory(fake()->numberBetween(0, 3))
                ->for($user)
                ->create([
                    'shipping_address_line_1' => $address->address_line_1,
                    'shipping_address_line_2' => $address->address_line_2,
                    'shipping_city' => $address->city,
                    'shipping_province' => $address->province,
                    'shipping_postal_code' => $address->postal_code,
                ])
                ->each(function (Order $order) use ($products) {
                    foreach (fake()->randomElements($products->all(), fake()->numberBetween(1, 4)) as $product) {
                        $order->addProduct($product, fake()->numberBetween(1, 3));
                    }

                    $order->recalculateTotal();
                });
        });

        $this->addCustomerExamples();
    }

    private function addCustomerExamples(): void
    {
        $customer = User::where('email', 'customer@musicstore.test')->first();
        $address = $customer->client->defaultAddress;
        [$wished, $bought] = Product::active()->orderBy('id')->take(2)->get();

        $customer->wishlist()->syncWithoutDetaching([$wished->id => ['notify_on_discount' => true]]);

        foreach ([OrderStatus::Pending, OrderStatus::Paid] as $status) {
            $order = Order::factory()->for($customer)->create([
                'status' => $status,
                'shipping_address_line_1' => $address->address_line_1,
                'shipping_address_line_2' => $address->address_line_2,
                'shipping_city' => $address->city,
                'shipping_province' => $address->province,
                'shipping_postal_code' => $address->postal_code,
            ]);

            $order->addProduct($bought);
            $order->recalculateTotal();
        }
    }
}

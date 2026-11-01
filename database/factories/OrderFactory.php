<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'MS-'.fake()->unique()->numerify('######'),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'shipping_cost' => fake()->randomElement([0, 4.95, 9.95]),
            'total' => 0,

            'shipping_address_line_1' => fake()->streetAddress(),
            'shipping_address_line_2' => null,
            'shipping_city' => fake()->city(),
            'shipping_province' => fake()->state(),
            'shipping_postal_code' => fake()->numerify('#####'),

            'payment_method' => fn (array $a) => $this->isPaid($a['status']) ? 'paypal' : null,
            'payment_reference' => fn (array $a) => $this->isPaid($a['status']) ? fake()->bothify('PAYID-##########') : null,
            'paid_at' => fn (array $a) => $this->isPaid($a['status']) ? fake()->dateTimeBetween('-6 months') : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }

    private function isPaid(OrderStatus|string $status): bool
    {
        $status = $status instanceof OrderStatus ? $status : OrderStatus::from($status);

        return in_array($status, [OrderStatus::Paid, OrderStatus::Preparing, OrderStatus::Shipped, OrderStatus::Delivered]);
    }
}

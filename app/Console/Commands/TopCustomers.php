<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class TopCustomers extends Command
{
    protected $signature = 'customers:top {--min=500 : Gasto mínimo en euros}';

    protected $description = 'Lista los clientes que han gastado al menos la cantidad indicada';

    public function handle(): int
    {
        $min = $this->option('min');

        if (! is_numeric($min) || $min < 0) {
            $this->error('El gasto mínimo debe ser un número igual o mayor que 0.');

            return self::FAILURE;
        }

        $clients = Client::with('user')
            ->withTotalSpent()
            ->spentAtLeast((float) $min)
            ->orderByDesc('total_spent')
            ->get();

        if ($clients->isEmpty()) {
            $this->info('Ningún cliente llega a ese gasto.');

            return self::SUCCESS;
        }

        $this->table(
            ['Cliente', 'Email', 'Total gastado'],
            $clients->map(fn (Client $client) => [
                $client->full_name,
                $client->user->email,
                number_format((float) $client->total_spent, 2, ',', '.').' €',
            ]),
        );

        return self::SUCCESS;
    }
}

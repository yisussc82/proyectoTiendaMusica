<?php

namespace App\Console\Commands;

use App\Services\OrderStatusService;
use Illuminate\Console\Command;

class CancelStaleOrders extends Command
{
    protected $signature = 'orders:cancel-stale {--hours=48 : Horas que puede estar un pedido pendiente}';

    protected $description = 'Cancela los pedidos pendientes más antiguos que el número de horas indicado';

    public function handle(OrderStatusService $orderStatus): int
    {
        $hours = (int) $this->option('hours');

        if ($hours < 1) {
            $this->error('Las horas deben ser un número mayor que 0.');

            return self::FAILURE;
        }

        $cancelled = $orderStatus->cancelStale($hours);

        $this->info("Pedidos cancelados: {$cancelled}");

        return self::SUCCESS;
    }
}

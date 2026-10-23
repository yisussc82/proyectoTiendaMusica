<?php

namespace App\Enums;

/**
 * Ciclo de vida de un pedido:
 * pending → confirmed → paid → preparing → shipped → delivered
 * (se puede cancelar mientras no se haya enviado)
 */
enum OrderStatus: string
{
    case Pending = 'pending';       // creado, aún sin confirmar
    case Confirmed = 'confirmed';   // confirmado: se descuenta el stock
    case Paid = 'paid';             // pago recibido
    case Preparing = 'preparing';   // en preparación en el almacén
    case Shipped = 'shipped';       // enviado
    case Delivered = 'delivered';   // entregado
    case Cancelled = 'cancelled';   // cancelado: se repone el stock
}

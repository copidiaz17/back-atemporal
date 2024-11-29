<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\VentaDetalle;

class DeterminarTotalDetalleVenta
{
    public function execute(VentaDetalle $ventaDetalle): float
    {
        return $ventaDetalle->venta_precio * $ventaDetalle->venta_cantidad;
    }
}

<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Venta;

class DeterminarTotalVenta
{
    public function __construct(
        private readonly DeterminarTotalDetalleVenta $determinarTotalDetalleVenta,
    ) {}

    public function execute(Venta $venta): float
    {
        $venta->loadMissing('detalles');
        $detalles = $venta->detalles;

        $total = 0;

        foreach ($detalles as $detalle) {
            $total += $this->determinarTotalDetalleVenta->execute($detalle);
        }

        return $total;
    }
}

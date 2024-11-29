<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasResumen extends BaseWidget
{
    protected function getStats(): array
    {

        $ventaGeneral = Venta::with('detalles')->get();
        $ventas = $ventaGeneral->count();
        $total = 0;
        foreach ($ventaGeneral as $venta) {
            foreach ($venta->detalles as $detalle) {
                $total +=  $detalle->venta_total;
            }
        }
        return [
            //
            Stat::make('Ventas', $ventas),
            Stat::make('Monto total', '$' . $total),
        ];
    }
}

<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Models\venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasResumen extends BaseWidget
{
    protected function getStats(): array
    {

        $ventaGeneral = venta::with('detalles')->get();
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
            Stat::make('Monto total', '$'.$total),
            Stat::make('Unique views', '192.1k'),
            Stat::make('Unique views', '192.1k'),
        ];
    }
}

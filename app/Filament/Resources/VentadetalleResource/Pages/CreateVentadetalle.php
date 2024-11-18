<?php

namespace App\Filament\Resources\VentadetalleResource\Pages;

use App\Filament\Resources\VentadetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVentadetalle extends CreateRecord
{
    protected static string $resource = VentadetalleResource::class;

    protected function saved(): void
    {
        parent::saved();

       

        $ventaDetalle = $this->record;

      
        $ventaDetalle->venta_precio = $ventaDetalle->producto->producto_precio; // Recalcular el precio si es necesario
        $ventaDetalle->venta_total = $ventaDetalle->venta_precio * $ventaDetalle->venta_cantidad; // Recalcular el total
        $ventaDetalle->save();
    }
}

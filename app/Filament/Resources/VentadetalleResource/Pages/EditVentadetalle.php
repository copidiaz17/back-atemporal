<?php

namespace App\Filament\Resources\VentadetalleResource\Pages;

use App\Filament\Resources\VentadetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVentadetalle extends EditRecord
{
    protected static string $resource = VentadetalleResource::class;

    protected function afterUpdate(): void
    {
        parent::afterUpdate();

        // Aquí puedes hacer cualquier acción después de actualizar el VentaDetalle
        // Por ejemplo, recalcular el total o hacer alguna acción en los modelos relacionados.

        // Acceso al registro actualizado
        $ventaDetalle = $this->record;

        // Realiza alguna acción después de actualizar el detalle de la venta
        $ventaDetalle->venta_total = $ventaDetalle->venta_precio * $ventaDetalle->venta_cantidad; // Recalcular el total
        $ventaDetalle->save(); // Guardar el registro después de las actualizaciones
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

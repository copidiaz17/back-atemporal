<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function afterSave(): void
    {
        DB::transaction(function () {
            $venta = $this->record;

            // Obtener los detalles relacionados con la venta recién creada
            $detalles = VentaDetalle::where('venta_id', $venta->id)->get();

            foreach ($detalles as $detalle) {
                // Obtener el producto asociado a este detalle
                $producto = Producto::find($detalle->producto_id);

                if ($producto) {
                    // Verificar si hay suficiente stock
                    if ($producto->producto_cantidad < $detalle->venta_cantidad) {
                        throw new \Exception(
                            "El producto {$producto->producto_nombre} no tiene suficiente stock. Stock actual: {$producto->producto_cantidad}."
                        );
                    }

                    // Reducir el stock del producto
                    $producto->producto_cantidad -= $detalle->venta_cantidad;
                    $producto->save(); // Guardar el cambio en la base de datos
                } else {
                    throw new \Exception("Producto con ID {$detalle->producto_id} no encontrado.");
                }
            }
        });
    }
}

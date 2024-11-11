<?php

namespace App\Filament\Resources\VentadetalleResource\Pages;

use App\Filament\Resources\VentadetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVentadetalle extends EditRecord
{
    protected static string $resource = VentadetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

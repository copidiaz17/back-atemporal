<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaDetalleResource\Pages;
use App\Models\Producto;
use App\Models\Venta;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class VentadetalleResource extends Resource
{
    protected static ?string $model = 'App\\Models\\VentaDetalle';

    protected static ?string $navigationLabel = 'Detalles de Venta';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('venta_id')
                    ->label('Venta')
                    // Ahora seleccionamos las ventas basadas en la relación con el cliente
                    ->options(Venta::pluck('id', 'id')->toArray())
                    ->required(),

                Forms\Components\Select::make('producto_id')
                    ->label('Producto')
                    // Cambié 'producto_nombre' por el nombre correcto del campo
                    ->options(Producto::whereNotNull('producto_nombre')->pluck('producto_nombre', 'id')->prepend('Selecciona un producto', null))
                    ->reactive() // Campo reactivo
                    ->afterStateUpdated(function (callable $set, $state) {
                        $producto = Producto::find($state);
                        if ($producto) {
                            $set('venta_precio', $producto->producto_precio); // Setea el precio automáticamente
                        }
                    })
                    ->required(),

                Forms\Components\TextInput::make('venta_cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->required()
                    ->reactive() // Campo reactivo para recalcular el total
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $precio = $get('venta_precio');
                        $cantidad = $get('venta_cantidad');
                        if ($precio && $cantidad) {
                            $set('venta_total', $precio * $cantidad); // Calcula el total
                        }
                    }),

                Forms\Components\TextInput::make('venta_precio')
                    ->label('Precio')
                    ->disabled()
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('venta_total')
                    ->label('Total')
                    ->disabled()
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.producto_nombre')->label('Producto'),
                TextColumn::make('venta_cantidad')->label('Cantidad'),
                TextColumn::make('venta_precio')->label('Precio'),
                TextColumn::make('venta_total')->label('Total'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentaDetalles::route('/'),
            'create' => Pages\CreateVentaDetalle::route('/create'),
            'edit' => Pages\EditVentaDetalle::route('/{record}/edit'),
        ];
    }
}
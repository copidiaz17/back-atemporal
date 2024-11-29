<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Closure;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->required()
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                DatePicker::make('venta_fecha')
                    ->default(Carbon::now())
                    ->disabledOn('edit')
                    ->required(),
                Repeater::make('detalles')
                    ->relationship('detalles')
                    ->label('Productos')
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::all()->pluck('producto_nombre', 'id'))
                            ->reactive()
                            ->required()
                            ->distinct()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $producto = Producto::find($state);
                                $set('venta_precio', $producto?->producto_precio ?? 0);
                                $set('stock_disponible', $producto?->producto_cantidad ?? 0);
                                $set('venta_cantidad', 1);
                            }),
                        TextInput::make('venta_cantidad')
                            ->label('Cantidad')
                            ->required()
                            ->numeric()
                            ->reactive()
                            // ->rules([
                            //     fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            //         $stockDisponible =  Producto::find($get('producto_id'))->producto_cantidad;
                            //         if ($value > $stockDisponible) {
                            //             $fail("La cantidad no está disponible en stock");
                            //         }
                            //     },
                            // ]),
                        ->afterStateUpdated(function (callable $set, $get, $state) {
                            $stockDisponible = Producto::find($get('producto_id'))?->producto_cantidad ?? 0;

                            $set('venta_total', $state * $get('venta_precio'));

                            if ($state > $stockDisponible) {
                                $set('venta_cantidad', $stockDisponible);
                                
                                // throw new \Exception("La cantidad excede el stock disponible: {$stockDisponible}");
                            }
                        }),

                        TextInput::make('venta_precio')
                            ->label('Precio Unitario')
                            ->required()
                            ->disabled(false)
                            ->prefix('$')
                            ->suffix('ARS')
                            ->mask(RawJs::make('$money($input)'))
                            ->dehydrated(),
                        TextInput::make('venta_total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->suffix('ARS')
                            ->mask(RawJs::make('$money($input)'))
                            ->disabled(),
                        // TextInput::make('stock_disponible')
                        //     ->label('Stock Disponible')
                        //     ->disabled()
                        //     ->hiddenOn('create'),
                    ])
                    ->columnSpan(2)
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID Venta'),
                TextColumn::make('cliente.name')->label('Cliente'),
                TextColumn::make('cliente.email')->label('Correo Electrónico'),
                TextColumn::make('detalles')->label('Productos Vendidos')->html()
                    ->formatStateUsing(function (Venta $record) {
                        $record->load('detalles.producto');
                        return $record->detalles->map(function ($detalle) {
                            return sprintf(
                                '<div>%s - Cantidad: %d - Total: $%.2f</div>',
                                $detalle->producto->producto_nombre ?? 'Producto no encontrado',
                                $detalle->venta_cantidad,
                                $detalle->venta_total
                            );
                        })->implode('');
                    }),
                TextColumn::make('detalles.venta_total')->label('Monto Total')->html()
                    ->formatStateUsing(function (Venta $record) {
                        $totalMonto = $record->detalles->sum('venta_total');
                        return sprintf('<div>$%.2f</div>', $totalMonto);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label(''),
                Tables\Actions\ViewAction::make()
                ->label(''),
                Tables\Actions\DeleteAction::make()
                ->label(''),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function afterSave($record): void
    {
        // Obtener los detalles de la venta
        $detalles = $record->detalles;

        foreach ($detalles as $detalle) {
            // Obtener el producto asociado
            $producto = Producto::find($detalle->producto_id);

            if ($producto) {
                // Verificar si hay suficiente stock
                if ($producto->producto_cantidad < $detalle->venta_cantidad) {
                    // throw new \Exception("No hay suficiente stock para el producto: {$producto->producto_nombre}");
                }

                // Reducir el stock
                $producto->producto_cantidad -= $detalle->venta_cantidad;
                $producto->save(); // Guardar los cambios en la base de datos
            }
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

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
                Fieldset::make()
                    ->label('Productos')
                    ->schema([
                        Repeater::make('detalles')
                            ->relationship('detalles')
                            ->label('')
                            ->addActionLabel('Añadir otro producto')
                            ->schema([
                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->options(Producto::query()
                                        ->pluck('producto_nombre', 'id'))
                                    ->reactive()
                                    ->required()
                                    ->distinct()
                                    ->afterStateUpdated(function (callable $set, $get, $state) {
                                        $producto = Producto::find($state);
                                        $set('venta_precio', $producto?->producto_precio ?? 0);
                                        $set('stock_disponible', $producto?->producto_cantidad ?? 0);
                                        $set('venta_cantidad', 1);

                                        $set('venta_total', $state * $get('venta_precio'));
                                    }),
                                TextInput::make('venta_cantidad')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->rules([
                                        fn(Get $get, ?Model $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record) {
                                            $stockDisponible =  Producto::find($get('producto_id'))->producto_cantidad;
                                            $cantidadActual = $record->venta_cantidad ?? 0;

                                            if ($cantidadActual == $value || $cantidadActual > $value) {
                                                return;
                                            }

                                            if ($stockDisponible == 0) {
                                                $fail("No hay mas stock disponible para este producto");
                                            }

                                            if ($value > $stockDisponible + $cantidadActual) {
                                                $fail("Solamente hay {$stockDisponible} unidades disponibles");
                                            }
                                        },
                                    ]),
                                TextInput::make('venta_precio')
                                    ->label('Precio Unitario')
                                    ->required()
                                    ->disabled(false)
                                    ->prefix('$')
                                    ->suffix('ARS')
                                    // ->mask(RawJs::make('$money($input)'))
                                    ->dehydrated(),
                                TextInput::make('venta_total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('$')
                                    ->suffix('ARS')
                                    // ->mask(RawJs::make('$money($input)'))
                                    ->disabled(),
                            ])
                            ->columnSpan(2)
                            ->columns(2)
                    ])
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
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

            // Reducir el stock
            $producto->producto_cantidad -= $detalle->venta_cantidad;
            $producto->save();
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

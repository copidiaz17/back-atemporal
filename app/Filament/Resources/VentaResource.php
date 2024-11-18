<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                    ->required()
                    ->disabledOn('edit'),
                Repeater::make('detalles')
                    ->relationship('detalles')
                    ->label('Productos')
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::all()->pluck('producto_nombre', 'id'))
                            ->reactive()
                            ->distinct()
                            ->required()
                            ->afterStateUpdated(
                                fn(callable $set, $state) =>
                                $set('venta_precio', Producto::find($state)?->producto_precio ?? 0)
                            ),
                        TextInput::make('venta_cantidad')
                        ->required()
                            ->label('Cantidad'),
                        TextInput::make('venta_precio')
                            ->label('Precio')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        TextInput::make('venta_total')
                            ->label('Total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->afterStateUpdated(
                                fn(callable $set, $get) =>
                                $set('venta_total', $get('venta_cantidad') * $get('venta_precio'))
                            )
                    ])
                    ->columnSpan(2)
                    ->columns(2)
            ]);
    }

    public static function table(Tables\Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')->label('ID Venta'),
                TextColumn::make('cliente.name')->label('Cliente'),
                TextColumn::make('cliente.email')->label('Correo Electrónico'),
                Tables\Columns\TextColumn::make('detalles')
                    ->label('Productos Vendidos')
                    ->html()
                    ->formatStateUsing(function (Venta $record) {
                        // Asegúrate de cargar la relación 'detalles' para evitar consultas adicionales
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
                Tables\Columns\TextColumn::make('total_monto')
                    ->label('Monto Total')
                    ->formatStateUsing(function (Venta $record) {
                        // El total se calcula directamente sobre la colección de detalles
                        $totalMonto = $record->detalles->sum('venta_total');
                        // Verificación opcional de errores
                        if ($totalMonto === null) {
                            return 'Error al calcular el total';
                        }

                        return '$' . number_format($totalMonto, 2);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('detalles'); // Carga eager de detalles
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

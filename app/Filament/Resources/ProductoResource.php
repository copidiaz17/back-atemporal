<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\ImageInput;
use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Categoria;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationGroup = 'Productos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('producto_nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre'),
                Forms\Components\TextInput::make('producto_descripcion')
                    ->required()
                    ->maxLength(255)
                    ->label('Descripcion'),

                Forms\Components\FileUpload::make('producto_imagen')
                    ->label('Imagen del Producto')
                    ->required()
                    ->image()
                    ->directory('images/productos')
                    ->disk('public'),
                Forms\Components\TextInput::make('producto_precio')
                    ->required()
                    ->numeric()
                    ->label('Precio'),
                Forms\Components\Select::make('categoria_id')
                    ->label('Categoría')
                    ->required()
                    ->options(Categoria::all()->pluck('categoria_nombre', 'id'))
                    // ->relationship('categoria', 'categoria_nombre')
                    ->searchable(),
                Forms\Components\TextInput::make('producto_cantidad')
                    ->required()
                    ->integer()
                    ->label('Cantidad'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('producto_nombre')
                    ->searchable()
                    ->label('Nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock.cantidad')
                    ->label('Cantidad')
                    ->sortable()
                    ->default(0)
                    ->getStateUsing(fn($record) => $record->stock?->cantidad ?? 0),
                //Tables\Columns\TextColumn::make('producto_descripcion')
                //  ->searchable()
                //->label('Descripcion'),
                Tables\Columns\ImageColumn::make('producto_imagen')
                    ->label('Imagen')
                    ->circular(),
                //->disk('public')
                //->url(fn ($record) => $record->producto_imagen ? url('storage/images/productos/' . $record->producto_imagen) : url('images/productos/default-image.jpg')),
                Tables\Columns\TextColumn::make('producto_cantidad')
                    ->label('Cantidad')
                    ->sortable(),

                Tables\Columns\TextColumn::make('producto_precio')
                    ->numeric()
                    ->sortable()
                    ->label('Precio'),
                Tables\Columns\TextColumn::make('categoria.categoria_nombre')
                    ->label('Categoría')
                    ->sortable()
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}

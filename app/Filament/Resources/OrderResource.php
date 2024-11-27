<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Log;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('wilaya')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('commune')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Repeater::make('products')
                ->schema([
                    Forms\Components\Select::make('product')
                    ->live()
                    ->required()
                    ->options(fn () => Product::all()->pluck('title', 'id')->toArray()),


                    Forms\Components\Select::make('color')
                    ->live()
                    ->options(
            function(Get $get) {
                        if($get('product')){
                            $product = Product::find($get('product'));
                            if($product->type === 'COLORS' || $product->type === 'COLORS_AND_SIZES'){
                                Log::info('colors are : ' .$product->colors);
                                return $product->colors->pluck('name','id')->toArray();
                            }
                        }
                     }
                    )
                    ->hidden(
                        function (Get $get) {
                            if($get('product')){
                                $product = Product::find($get('product'));
                                if($product->type !== 'COLORS' && $product->type !== 'COLORS_AND_SIZES'){
                                    return true;
                                } 
                                else {
                                    return false;
                                }
                            }
                            else {
                                return true;
                            }
                        }
                    )

                    ,
                    Forms\Components\Select::make('size')
                    ->options(
                        function(Get $get) {
                            if($get('product')){
                                $product = Product::find($get('product'));
                                if($product->type === 'SIZES'){
                                     return [];
                                }
                                else if ($product->type === 'COLORS_AND_SIZES'){
                                    if($get('color')){
                                        Log::info('color choosen is : ' . $get('color'));
                                        return [];
                                    }
                                    else {
                                        return [];
                                    }
                                }
                            }
                         }
                    )
                    ->hidden(
                        function (Get $get) {
                            if($get('product')){
                                $product = Product::find($get('product'));
                                if($product->type !== 'SIZES' && $product->type !== 'COLORS_AND_SIZES'){
                                    return true;
                                } 
                                else {
                                    return false;
                                }
                            }
                            else {
                                return true;
                            }
                        }
                    )
                    ,
                    Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                ])    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wilaya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commune')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

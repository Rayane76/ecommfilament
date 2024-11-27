<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->suffix('DA'),
                Forms\Components\Select::make('type')
                ->options([
                    'SIMPLE' => 'Simple',
                    'COLORS' => 'Colors',
                    'SIZES'  => 'Sizes',
                    'COLORS_AND_SIZES' => 'Colors And Sizes'
                ])
                ->required()
                ->disabledOn('edit')
                ,    
                Forms\Components\FileUpload::make('mainImage')
                    ->required()
                    ->disk('cloudinary')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg' , 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff'])
                    ->getUploadedFileNameForStorageUsing(
                      fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->chopEnd(['.png','.jpeg','.jpg','.gif','.webp','.svg+xml','.bmp','.tiff'])->prepend(Str::random(8))
                    ),                 
                Forms\Components\FileUpload::make('images')
                    ->nullable()
                    ->multiple()
                    ->disk('cloudinary')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg' , 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff'])
                    ->getUploadedFileNameForStorageUsing(
                      fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->chopEnd(['.png','.jpeg','.jpg','.gif','.webp','.svg+xml','.bmp','.tiff'])->prepend(Str::random(8))
                    ),                 
                Forms\Components\Toggle::make('isOut')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('DZD' , locale: 'DA')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('mainImage')
                    ->disk('cloudinary'),
                Tables\Columns\ToggleColumn::make('isOut')
                ,    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->after(function ($records) {
                        $records->each(function ($record) {
                            Storage::disk('cloudinary')->delete($record->mainImage);
                            if($record->images){
                            Storage::disk('cloudinary')->delete($record->images);
                            }
                        });
                    })
                    ,
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\ColorsRelationManager::class,
            RelationManagers\SizesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

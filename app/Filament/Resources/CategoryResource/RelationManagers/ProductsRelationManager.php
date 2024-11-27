<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
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
            Forms\Components\FileUpload::make('mainImage')
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
            Tables\Columns\TextColumn::make('title')
                ->searchable(),
            Tables\Columns\TextColumn::make('price')
                ->money('DZD' , locale: 'DA')
                ->sortable(),
            Tables\Columns\ImageColumn::make('mainImage')
                ->disk('cloudinary'),
            Tables\Columns\ToggleColumn::make('isOut')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function ($data): array {
                    $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];

                    if($data['images']){
                      $modified = array_map(function ($str) {
                          return 'filamentecomm/' . $str;
                       }, $data['images']);
               
                       $data['images'] = $modified;
                    }
    
                     return $data;
                })
                ,
                AttachAction::make()
                ->recordSelectOptionsQuery(function (Builder $query) {
                    return $query->select(['id', 'title'])->orderBy('title');
                })
                ->preloadRecordSelect()
                ->multiple()
            ])
            ->actions([
                DetachAction::make(),
                Tables\Actions\EditAction::make()
                ->url(function ($record){
                    return ProductResource::getUrl('edit', [$record]);
                })
                ,
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

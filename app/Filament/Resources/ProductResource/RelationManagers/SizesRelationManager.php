<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;



class SizesRelationManager extends RelationManager
{
    protected static string $relationship = 'sizes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type == 'SIZES';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('mainImage')
                    ->nullable()
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('mainImage')
                ->disk('cloudinary'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function(array $data): array {
                    if($data['mainImage']){
                        $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];
                        }
                
                        if($data['images']){
                            $modified = array_map(function ($str) {
                                return 'filamentecomm/' . $str;
                             }, $data['images']);
                     
                             $data['images'] = $modified;
                        }
                    
                    return $data;
                })
                ,
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                ->after(function ($record) {
                    if($record->mainImage){
                        Storage::disk('cloudinary')->delete($record->mainImage);
                    }
                    if($record->images){
                        Storage::disk('cloudinary')->delete($record->images);
                    }
                    })
                ,
                Tables\Actions\EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {

                    $infos = $this->getMountedTableActionRecord();

                    $mainImage = $infos->mainImage;


                    if($mainImage && $data['mainImage']){
                      if($mainImage !== $data['mainImage']){
                            $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];
                            Storage::disk('cloudinary')->delete($mainImage);
                      }
                    }
             
                    else if ($data['mainImage']) {
                       $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];
                    }
             
                    else if ($mainImage) {
                     Storage::disk('cloudinary')->delete($mainImage);
                    }
             
             
             
                    $images = $infos->images;
             
                    //case both are not null
                    if($data['images'] && $images) {
                       $isEqual = Arr::sort($data['images']) == Arr::sort($images);
                       if(!$isEqual){
                         $modified = array_map(function ($str) {
                             if (!str_starts_with($str, 'filamentecomm/')) {
                                 return 'filamentecomm/' . $str;
                             }   
                             // If the string already starts with 'lara_test/', return it unchanged
                             return $str;
                            }, $data['images']);
                     
                            $data['images'] = $modified;
                     
                            $array_of_images_to_delete = array_diff($images, $modified);
                     
                            Storage::disk('cloudinary')->delete($array_of_images_to_delete);
                       }
                    }
                    //case it was null
                    else if ($data['images']){
                     $modified = array_map(function ($str) {
                         return 'filamentecomm/' . $str;
                      }, $data['images']);
              
                      $data['images'] = $modified;
                    }
                    //case it wasn't null but now it is
                    else if ($images){
                        Storage::disk('cloudinary')->delete($images);
                    }
             
                    return $data;
                })
                ,
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->after(function ($records) {
                        $records->each(function ($record) {
                        if($record->mainImage){
                            Storage::disk('cloudinary')->delete($record->mainImage);
                        }
                        if($record->images){
                            Storage::disk('cloudinary')->delete($record->images);
                        }
                        });
                    })
                    ,
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;


class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Runs before the form fields are saved to the database.

       $mainImage = $this->record->mainImage;

       if($mainImage !== $data['mainImage']){
             $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];
             Storage::disk('cloudinary')->delete($mainImage);
       }


       $images = $this->record->images;

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
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->after(function ($record) {
                Storage::disk('cloudinary')->delete($record->mainImage);
                if($record->images){ 
                   Storage::disk('cloudinary')->delete($record->images);
                }
           })
            ,
        ];
    }
}

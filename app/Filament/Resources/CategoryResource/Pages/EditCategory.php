<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Runs before the form fields are saved to the database.

       $image = $this->record->image;

       if($data['image'] || $image) {
          if($image !== $data['image']){
                if($data['image']){
                   $data['image'] = 'filamentecomm/' . $data['image'];
                }
                if($image){
                Storage::disk('cloudinary')->delete($image);
                }
            }
       }

       return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->after(function ($record) {
                 if($record->image){
                    Storage::disk('cloudinary')->delete($record->image);
                 }
            })
            ,
        ];
    }
}

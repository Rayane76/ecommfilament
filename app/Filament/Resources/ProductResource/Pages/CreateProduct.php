<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //save public_id
        $data['mainImage'] = 'filamentecomm/' . $data['mainImage'];

        if($data['images']){
            $modified = array_map(function ($str) {
                return 'filamentecomm/' . $str;
             }, $data['images']);
     
             $data['images'] = $modified;
        }
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = ProductResource::class;
}

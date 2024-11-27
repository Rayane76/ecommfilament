<?php

namespace App\Filament\Resources\SizeResource\Pages;

use App\Filament\Resources\SizeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSize extends CreateRecord
{

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //save public_id
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
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = SizeResource::class;
}

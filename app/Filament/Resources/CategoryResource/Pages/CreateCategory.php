<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //save public_id
        if($data['image']){
           $data['image'] = 'filamentecomm/' . $data['image'];
        }
    
        return $data;
    }

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

    protected static string $resource = CategoryResource::class;
}

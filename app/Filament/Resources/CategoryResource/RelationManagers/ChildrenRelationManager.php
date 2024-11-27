<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $inverseRelationship = 'parents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ,
            Forms\Components\FileUpload::make('image')
              ->nullable()
              ->disk('cloudinary')
              ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg' , 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff'])
              ->getUploadedFileNameForStorageUsing(
                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->chopEnd(['.png','.jpeg','.jpg','.gif','.webp','.svg+xml','.bmp','.tiff'])->prepend(Str::random(8))
            )                 
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                ->disk('cloudinary')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                ->recordSelectOptionsQuery(function(Builder $query) {
                    $record = $this->getOwnerRecord();
                    $ancIds = Category::getAncestorIds($record->id);
                    $query->where('categories.id','!=',$record->id)
                    ->whereNotIn('categories.id', $ancIds);
                })
                ->preloadRecordSelect()->multiple(),
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function(array $data): array {
                    if($data['image']){
                        $data['image'] = 'filamentecomm/' . $data['image'];
                     }
                 
                     return $data;
                })
                ,
            ])
            ->actions([
                DetachAction::make(),
                Tables\Actions\EditAction::make()
                ->url(function ($record){
                    return CategoryResource::getUrl('edit', [$record]);
                })
                ,
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DetachBulkAction::make()
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

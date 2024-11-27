<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Size;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;



class ColorsRelationManager extends RelationManager
{
    protected static string $relationship = 'colors';


    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
{
    return $ownerRecord->type == 'COLORS' || $ownerRecord->type == 'COLORS_AND_SIZES';
}

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\ColorPicker::make('hex')
                ->required(),
            Forms\Components\Toggle::make('isOut'),    
            Forms\Components\Repeater::make('sizes')
            ->schema([
                Forms\Components\Select::make('size')->required()
                ->options(fn () => Size::whereNull('product_id')->pluck('name', 'id')->toArray()),
                Forms\Components\TextInput::make('stock')->numeric()->nullable(),
                Forms\Components\Toggle::make('isOut')
            ])
            ->hidden(
                function (): bool {
                    $product = $this->getOwnerRecord();
                    
                    return $product->type !== 'COLORS_AND_SIZES';
                }
            )
            ->required(
                function (): bool {
                    $product = $this->getOwnerRecord();
                    
                    return $product->type === 'COLORS_AND_SIZES';
                }
            )
            ->default([])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('hex'),
                ToggleColumn::make('isOut'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                ->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Toggle::make('isOut')->required(),
                    Forms\Components\Repeater::make('sizes')
                    ->schema([
                        Forms\Components\Select::make('size')->required()
                        ->options(Size::whereNull('product_id')->get()->pluck('name','id')),
                        Forms\Components\TextInput::make('stock')->numeric()->nullable(),
                        Forms\Components\Toggle::make('isOut')
                    ])->hidden(
                        function (): bool {
                            $product = $this->getOwnerRecord();
                            
                            return $product->type !== 'COLORS_AND_SIZES';
                        }
                    )
                    ->required(
                        function (): bool {
                            $product = $this->getOwnerRecord();
                            
                            return $product->type === 'COLORS_AND_SIZES';
                        }
                    )
                ])
                ->preloadRecordSelect()
                ,
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['sizes'] = json_decode($data['sizes'] ?? '[]', true);
                    return $data;
                })
                ,
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

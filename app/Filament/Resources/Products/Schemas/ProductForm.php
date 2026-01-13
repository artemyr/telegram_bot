<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Toggle::make('exists')
                    ->required(),
                TextInput::make('expire_days')
                    ->numeric(),
                DateTimePicker::make('expire'),
                DateTimePicker::make('buy_at'),
                Select::make('store')
                    ->options(['fridge' => 'Fridge', 'grocery' => 'Grocery', 'other' => 'Other'])
                    ->default('other')
                    ->required(),
            ]);
    }
}

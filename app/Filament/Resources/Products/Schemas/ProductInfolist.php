<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                IconEntry::make('exists')
                    ->boolean(),
                TextEntry::make('expire_days')
                    ->numeric(),
                TextEntry::make('expire')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('buy_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('store')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

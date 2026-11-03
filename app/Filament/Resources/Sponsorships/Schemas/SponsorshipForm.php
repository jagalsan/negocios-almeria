<?php

namespace App\Filament\Resources\Sponsorships\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SponsorshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ranking_id')
                    ->relationship('ranking', 'title'),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                TextInput::make('slot')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required(),
                TextInput::make('amount_cents')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('paid_at'),
                TextInput::make('payment_link'),
                TextInput::make('status')
                    ->required()
                    ->default('scheduled'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}

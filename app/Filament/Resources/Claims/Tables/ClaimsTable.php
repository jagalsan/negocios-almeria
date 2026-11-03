<?php

namespace App\Filament\Resources\Claims\Tables;

use App\Actions\ApproveClaim;
use App\Models\Claim;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('company.name')->label('Empresa')->searchable(),
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('role_in_company')->label('Cargo'),
                TextColumn::make('phone')->label('Teléfono'),
                IconColumn::make('domain_verified_at')->label('Dominio verificado')->boolean(),
                TextColumn::make('status')->label('Estado')->badge(),
                TextColumn::make('created_at')->label('Recibida')->since()->sortable(),
            ])
            ->filters([SelectFilter::make('status')->options(['pending' => 'Pendiente', 'approved' => 'Aprobada', 'rejected' => 'Rechazada'])])
            ->recordActions([
                Action::make('approve')->label('Aprobar')->color('success')->requiresConfirmation()->visible(fn (Claim $record) => $record->status->value === 'pending')->action(function (Claim $record) {
                    app(ApproveClaim::class)->handle($record, auth()->user());
                    Notification::make()->title('Reclamación aprobada')->success()->send();
                }),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

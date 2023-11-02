<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ServiceResource;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery()
        ->join('departements', 'services.departement_id', 'departements.id')
        ->select('services.*', 'nom_departement');
    }
}

<?php

namespace App\Filament\Resources\PermanenceResource\Pages;

use Filament\Actions;
use App\Models\Service;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PermanenceResource;

class ListPermanences extends ListRecords
{
    protected static string $resource = PermanenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
           
        ];
    }


    protected function getTableQuery(): ?Builder
    {
        $loggedService = Service::where('id', auth()->user()->service_id)
            ->value('departement_id');

        return static::getResource()::getEloquentQuery()
            ->whereHas('departement', function ($query) use ($loggedService) {
                $query->where('departement_id', $loggedService);
            })
            ->join('departements', 'permanences.departement_id', 'departements.id')
            ->select('permanences.*', 'nom_departement as departement');

    }
}

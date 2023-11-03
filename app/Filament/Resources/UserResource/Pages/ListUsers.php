<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\Departement;
use App\Models\User;
use Filament\Actions;
use App\Models\Service;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    protected function getTableQuery(): ?Builder
    {

        $loggedUserId = auth()->user()->service_id;

        $loggedService = Service::where('id', $loggedUserId)->value('departement_id');



        return static::getResource()::getEloquentQuery()

            ->whereHas('service.departement', function ($query) use ($loggedService) {
                $query->where('id', $loggedService);
            })
            ->join('services', 'services.id','users.service_id')
            ->join('departements','departements.id','=','services.departement_id')
            ->select('users.*','services.nom_service as service');
;

        
    }
}

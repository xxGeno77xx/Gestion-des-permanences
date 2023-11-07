<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use Filament\Actions;
use App\Models\Service;
use App\Enums\PermissionsClass;
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
        $loggedUserId = auth()->user()->service_id;

        $loggedServiceID = Service::whereHas('users', function($query) use($loggedUserId){
            $query->where('id',$loggedUserId);
        })->value('departement_id');


        return static::getResource()::getEloquentQuery()
        ->where('departement_id', $loggedServiceID )
        ->join('departements', 'services.departement_id', 'departements.id')
        ->select('services.*', 'nom_departement');
    }


    protected function authorizeAccess(): void
    {
        $user = auth()->user();
    
        $userPermission = $user->hasAnyPermission([
            PermissionsClass::services_create()->value,
            PermissionsClass::services_read()->value,
            PermissionsClass::services_update()->value,
            // PermissionsClass::utilisateurs_delete()->value,

        ]);
    
        abort_if(! $userPermission, 403, __("Vous n'avez pas access à cette page"));
    }


    
}

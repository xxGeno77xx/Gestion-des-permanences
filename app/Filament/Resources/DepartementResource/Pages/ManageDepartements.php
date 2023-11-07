<?php

namespace App\Filament\Resources\DepartementResource\Pages;

use Filament\Actions;
use App\Enums\PermissionsClass;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\DepartementResource;

class ManageDepartements extends ManageRecords
{
    protected static string $resource = DepartementResource::class;

    protected function getHeaderActions(): array
    {
        $actions = array();

        if (auth()->user()->hasPermissionTo(PermissionsClass::utilisateurs_create()->value)) {
            $actions = [
                Actions\CreateAction::make()
            ];

        }
        return $actions;
    }


    // protected function authorizeAccess(): void
    // {
    //     $user = auth()->user();

    //     $userPermission = $user->hasAnyPermission([

    //         ,
    //         PermissionsClass::utilisateurs_read()->value,
    //         // PermissionsClass::utilisateurs_update()->value,
    //         // PermissionsClass::utilisateurs_delete()->value,

    //     ]);

    //     abort_if(!$userPermission, 403, __("Vous n'avez pas access à cette page"));
    // }
}

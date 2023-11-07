<?php

namespace App\Filament\Resources\PresenceResource\Pages;

use Filament\Actions;
use App\Enums\PermissionsClass;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PresenceResource;

class CreatePresence extends CreateRecord
{
    protected static string $resource = PresenceResource::class;

    protected function authorizeAccess(): void
    {
        $user = auth()->user();
    
        $userPermission = $user->hasAnyPermission([
            
            PermissionsClass::presences_create()->value,
            PermissionsClass::presences_read()->value,
            // PermissionsClass::utilisateurs_update()->value,
            // PermissionsClass::utilisateurs_delete()->value,

        ]);
    
        abort_if(! $userPermission, 403, __("Vous n'avez pas access à cette page"));
    }
}

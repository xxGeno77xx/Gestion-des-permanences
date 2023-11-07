<?php

namespace App\Filament\Resources\PermanenceResource\Pages;

use Filament\Actions;
use App\Models\Permanence;
use App\Enums\PermissionsClass;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PermanenceResource;
use App\Filament\Resources\PermanenceResource\Widgets\PermanenceList;


class EditPermanence extends EditRecord
{
    protected static string $resource = PermanenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('Télécharger')
            ->icon('heroicon-o-arrow-down-tray')
                    ->color('teal')
                    ->url(fn(Permanence $record) => route('permanence.pdf.ddl', $record))
                    ->openUrlInNewTab()
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            PermanenceList::class
        ];
    }

    protected function authorizeAccess(): void
    {
        $user = auth()->user();
    
        $userPermission = $user->hasAnyPermission([
            // PermissionsClass::utilisateurs_create()->value,
            PermissionsClass::permanences_read()->value,
            PermissionsClass::permanences_update()->value,
            PermissionsClass::permanences_delete()->value,

        ]);
    
        abort_if(! $userPermission, 403, __("Vous n'avez pas access à cette page"));
    }
}

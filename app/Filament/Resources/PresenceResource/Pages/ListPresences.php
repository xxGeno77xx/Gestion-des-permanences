<?php

namespace App\Filament\Resources\PresenceResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PresenceResource;

class ListPresences extends ListRecords
{
    protected static string $resource = PresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $loggedUser = User::where('id', auth()->user()->id)->value('id');

        return static::getResource()::getEloquentQuery()
            ->whereHas('user', function ($query) use ($loggedUser) {
                $query->where('id', $loggedUser);
            })
            ->join('users', 'presences.user_id', 'users.id')
            ->select('presences.*', 'users.name as nom');

    }
}

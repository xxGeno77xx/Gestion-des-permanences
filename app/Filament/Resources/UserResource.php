<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'Utilisateurs';


    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label("Nom")
                            ->required(),

                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(table: static::$model, ignorable: fn ($record) => $record)
                            ->regex('/.*@laposte\.tg$/') // field must end with @laposte.tg
                            ->label('Email'),

                        TextInput::make('password')
                            ->same('passwordConfirmation')
                            ->password()
                            ->maxLength(255)
                            ->required(fn ($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                            ->dehydrateStateUsing(fn ($state) => ! empty($state) ? Hash::make($state) : '')
                            ->label('Mot de passe'),

                        TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                            ->label("Confirmation de mot de passe"),

                        Select::make('service_id')
                            ->label('Service')
                            ->searchable()
                            ->options(
                                Service::select(['nom_service','id'])->pluck('nom_service','id')
                            )

                       
                    ])->columns(2),

            ]);
       
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),

                TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i:s')
                    ->label('Date d\'inscription'),

                TextColumn::make('service'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}

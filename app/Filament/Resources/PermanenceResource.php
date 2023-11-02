<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Service;
use Filament\Forms\Form;
use App\Models\Permanence;
use Filament\Tables\Table;
use App\Models\Departement;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermanenceResource\Pages;
use Awcodes\Curator\PathGenerators\DatePathGenerator;
use App\Filament\Resources\PermanenceResource\RelationManagers;
use App\Filament\Resources\PermanenceResource\Widgets\PermanenceList;

class PermanenceResource extends Resource
{
    protected static ?string $model = Permanence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {   
        $serviceConnecte = Service::where('id', auth()->user()->service_id)
                                                ->value('departement_id');
        return $form
            ->schema([

                Repeater::make('users')
                ->label('')
                    ->schema([
                        Select::make('participants')
                        ->label('Agents')
                        ->options(
                            User::whereHas('service', function ($query) use ($serviceConnecte) {
                                $query->where('departement_id', $serviceConnecte);
                            })->pluck('name','users.id')
                        )
                        ->multiple()
                        ->required()
                        
                    ])
                    ->deletable(false)
                    ->addable(false),
                DatePicker::make('date_debut')
                    ->required(),
                DatePicker::make('date_fin')
                    ->required(),
                Hidden::make('departement_id')
                    ->default(Departement::whereHas('services', function ($query) use ($serviceConnecte) {
                        $query->where('departement_id', $serviceConnecte);
                    })->value('id'))        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_debut')
                ->label('Année')
                ->date('Y'),

                TextColumn::make('departement')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Permanence $record) => route('permanence.pdf.ddl', $record))
                    ->openUrlInNewTab()
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     // Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPermanences::route('/'),
            'create' => Pages\CreatePermanence::route('/create'),
            'edit' => Pages\EditPermanence::route('/{record}/edit'),
        ];
    }   
    
    public static function getWidgets(): array
    {
        return [
            PermanenceList::class,
        ];
    }
}

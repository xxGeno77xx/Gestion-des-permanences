<?php

namespace App\Filament\Resources\PermanenceResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Actions;
use App\Models\Service;
use App\Enums\PermissionsClass;
use App\Functions\DateFunction;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PermanenceResource;

class CreatePermanence extends CreateRecord
{
    protected static string $resource = PermanenceResource::class;

    public function beforeCreate()
    {
        $data = $this->data;

        if (carbon::parse($data['date_debut'])->addDays(7)  >= $data['date_fin'])
        {

            Notification::make()
            ->warning()
            ->title('Attention')
            ->body('Vos dates doivent êtres séparées d\'au moins 7 jours')
            ->persistent()
            ->send();
    
            $this->halt();
        }
    }
    public function afterCreate()
    {
        $permanence = $this->record;


        $samedis = DateFunction::getDateForSpecificDayBetweenDates($permanence->date_debut, $permanence->date_fin, env('PERMANENCE'));

        //All services  with de the department of the logged in user
        $services = Service::where('departement_id', auth()->user()->service->departement_id)
            ->select('nom_service', 'id')
            ->get();


        // returns the IDs of the selected users (users column content)  as json boject 
        //if I remove the where clause, the query returns every single user in Database....no clue as to why tho
        $parmanenceAgentsJson = $permanence->select('users')
            ->where('id', $permanence->id)
            ->get();

        $participantsIds = [];

        foreach ($parmanenceAgentsJson as $key => $participant) {
            $participantData = json_decode($participant, true);

            for ($i = $key; $i < count($participantData['users'][$key]['participants']); $i++) {
                $participantsIds[] = $participantData['users'][$key]['participants'][$i]; // retrieving  Ids of participants from the users Json Column and putting them in an array
            }
        }

        foreach ($services as $service) {

            $tempUser = User::whereIn('id', $participantsIds)
                ->where('service_id', $service->id)
                ->get();

            foreach ($tempUser as $user) {
                $totalAgentsForPermanence[] = $user->id;
            }

        }

        $serviceAgents = [];

        foreach ($services as $key => $service) {
            $serviceAgents[$key] = array_splice($totalAgentsForPermanence, 0, User::whereIn('id', $participantsIds)->where('service_id', $service->id)
                ->count());
        }


        function interleaveArrays($multiArray, $nombre, $service)
        {
            $result = [];
        
            // Détermine le nombre maximum d'éléments dans les tableaux internes
            $maxCount = max(array_map(function ($array) {
                return count($array);
            }, $multiArray));
        
            $currentIndexs = array_fill(0, count($multiArray), 0);
        
            while (count($result) < $nombre * $service) {
                for ($s = 0; $s < $service; $s++) {
                    foreach ($multiArray as $key => $array) {
                        if (count($result) >= $nombre * $service) {
                            break 2;
                        }
                        $currentIndex = $currentIndexs[$key] % count($array);
                        $result[] = $array[$currentIndex];
                        $currentIndexs[$key]++;
                    }
                }
            }
        
            return $result;
        }
        $finalArray = [];

        $jsonToInsertIntoOrder = [];

        $finalArray = (interleaveArrays($serviceAgents, count($samedis), $services->count()));

        // dd($finalArray);
        $loopCounter = 0;
        foreach ($samedis as $key => $samedi) {

            for ($i = 0; $i < count($services); $i++) {
                if ($loopCounter == count($finalArray)) {
                    $loopCounter = 0;
                }

                $tempUser = $finalArray[$loopCounter];

                $usersToPickPerService[] = $tempUser;

                $loopCounter++;

            }

            $jsonToInsertIntoOrder[] = [
                "date" => carbon::parse($samedi)->translatedFormat('l, D-M-Y'),
                "participants" => $usersToPickPerService
            ];

            $usersToPickPerService = [];

            if ($key == count($samedis)) {
                break;
            }
        }

        if($jsonToInsertIntoOrder)
        {
            $permanence->update([
                'order' => ($jsonToInsertIntoOrder)
            ]);
        }
       
    }

    protected function authorizeAccess(): void
    {
        $user = auth()->user();
    
        $userPermission = $user->hasAnyPermission([
            
            PermissionsClass::permanences_create()->value,
            PermissionsClass::permanences_read()->value,
            // PermissionsClass::utilisateurs_update()->value,
            // PermissionsClass::utilisateurs_delete()->value,

        ]);
    
        abort_if(! $userPermission, 403, __("Vous n'avez pas access à cette page"));
    }

   
}

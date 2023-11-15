@php
    use App\Filament\Resources\PermanenceResource\Pages\CreatePermanence;
    use App\Models\User;
    use App\Models\Service;
    use App\Models\Departement;
    use App\Enums\StatesClass;
    use App\Models\Permanence;
    use App\Functions\DateFunction;
    use carbon\carbon;

    $lastKValue = null;

    $loggedService = Service::where('id', auth()->user()->service_id)->first();
    $participantsIds= array();

    $record = Permanence::join('departements', 'departements.id', 'permanences.departement_id')
        ->whereHas('departement', function ($query) use ($loggedService) {
            $query->where('departement_id', $loggedService->departement_id);
        })
        ->where('permanences.statut', StatesClass::Active()->value)
        ->orderBy('permanences.created_at', 'desc')
        ->first();

    if ($record) {
        // returns every order column
        $participantsOrder = $record
            ->select('order')
            ->where('id', $record->id)
            ->pluck('order');

            
        foreach ($participantsOrder as $key => $agentsTrioPerPermanenceDay) {
            $permanenceDayWithAgents = json_decode($agentsTrioPerPermanenceDay, true);

            // retrieving  Ids of participants from the orders Json Column and putting them in an array
            for ($i = $key; $i < count($permanenceDayWithAgents); $i++) {
                for ($j = 0; $j < count($permanenceDayWithAgents[$i]['participants']); $j++) {
                    $participantsIds[] = $permanenceDayWithAgents[$i]['participants'][$j];
                } 
            }
        }

        $usersArray = [];

        $services = Service::where('departement_id', auth()->user()->service->departement_id)
            ->select('nom_service')
            ->get();

        $departement = Departement::where('id', $record->departement_id)
            ->get()
            ->value('nom_departement');

        $dates = DateFunction::getDateForSpecificDayBetweenDates($record->date_debut, $record->date_fin, env('PERMANENCE'));

        $annee = carbon::parse($record->date_debut)->format('Y');

        $months = [];
        $days = [];
        $usersNames = [];
        $y = 0;
        $z = 0;

        foreach ($dates as $key => $date) {
            //putting months in an array
            if (!in_array(carbon::parse($date)->format('F'), $months)) {
                $months[] = carbon::parse($date)->format('F');
            }
            //putting days in an array
            if (!in_array(carbon::parse($date)->format('l, d-m-Y'), $days)) {
                $days[] = carbon::parse($date)->format('l, d-m-Y');
            }
        }

        foreach ($participantsIds as $key => $id) {
            $usersNames[] = User::where('id', $id)
                ->select('name', 'id')
                ->get()
                ->value('name');
        }
    }

@endphp

@if ($record)
    <x-filament-widgets::widget>
        <x-filament::section>
            
            <!-- component -->
            <h2 class="text-4xl  text-center py-6 font-extrabold dark:text-white">Planning des permanences pour les
                Samedis de l'année {{ $annee }}</h2>
            <h2 class="text-4xl  text-center py-2 font-extrabold dark:text-white">SPT / {{ $departement }}</h2>
            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Dates</th>
                            @foreach ($services as $service)
                                <th scope="col" class="px-6 py-4 font-medium text-gray-900">{{ $service->nom_service }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        @if ($dates)
                            @foreach ($months as $month)
                                <tr class="hover:bg-gray-50">
                                    <th class=" px-6 py-4 font-normal text-gray-900 text-center">
                                        {{ carbon::parse($month)->translatedFormat('F') }}
                                    </th>
                                </tr>
                                @foreach ($days as $day)
                                    @if (carbon::parse($day)->format('F') == $month)
                                        <tr class="hover:bg-gray-50">
                                            <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
                                                {{-- <div class="relative h-10 w-10">
                                            </div> --}}
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-700">
                                                        {{ carbon::parse($day)->translatedFormat('l, d F Y') }}</div>
                                                    {{-- <div class="text-gray-400">jobs@sailboatui.com</div> --}}
                                                </div>
                                            </th>
                                            @for ($k = 0; $k < $services->count(); $k++)
                                                <td class="px-6 py-4">
                                                    <span class="h-1.5 w-1.5 rounded-full">
                                                        {{ $usersNames[$y + $k] }}
                                                    </span>
                                                </td>
                                            @endfor
                                            @php
                                                $y = $y + $k;
                                            @endphp
                                        @elseif($loop->last)
                                        @break
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
@else
<x-filament-widgets::widget>
    <x-filament::section>
        Aucun calendrier de permanences à afficher
    </x-filament::section>
</x-filament-widgets::widget>
@endif

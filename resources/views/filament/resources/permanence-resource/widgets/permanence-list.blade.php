@php
    use App\Filament\Resources\PermanenceResource\Pages\CreatePermanence;
    use App\Models\User;
    use App\Models\Service;
    use App\Models\Departement;
    use App\Functions\DateFunction;
    use carbon\carbon;

    $lastKValue = null;
    $record = $this->record;

    // returns every order column
    $participantsOrder = $record
        ->select('order')
        ->where('id', $record->id)
        ->pluck('order');

    foreach ($participantsOrder as $key => $agentsTrioPerPermanenceDay) {
        $permanenceDayWithAgents = json_decode($agentsTrioPerPermanenceDay, true);

        for ($i = $key; $i < count($permanenceDayWithAgents); $i++) {
            for ($j = 0; $j < count($permanenceDayWithAgents[$i]['participants']); $j++) {
                $participantsIds[] = $permanenceDayWithAgents[$i]['participants'][$j];
            } // retrieving  Ids of participants from the orders Json Column and putting them in an array
        }
    }

    $usersArray = [];

    $services = Service::where('departement_id', auth()->user()->service->departement_id)
        ->select('nom_service')
        ->get();

    $departement = Departement::where('id',$record->departement_id)->get()->value('nom_departement') ;

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
@endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <!-- component -->
        <h2 class="text-4xl  text-center py-6 font-extrabold dark:text-white">Planning des permanences pour les Samedis de l'année {{$annee}}</h2>
        <h2 class="text-4xl  text-center py-2 font-extrabold dark:text-white" >SPT / {{ $departement }}</h2>
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
                                            <div class="relative h-10 w-10">
                                            </div>
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-700">{{( carbon::parse($day)->translatedFormat('l, d F Y')) }}</div>
                                                <div class="text-gray-400">jobs@sailboatui.com</div>
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



{{-- <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
  <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
    <thead class="bg-gray-50">
      <tr>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Dates</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Exploitation</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Etudes</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Réseaux & Systèmes</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
      <tr class="hover:bg-gray-50">
          <th class=" px-6 py-4 font-normal text-gray-900 text-center">
              MOIS
          </th>
      </tr>
      <tr class="hover:bg-gray-50">
        <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
          <div class="relative h-10 w-10">
            <img
              class="h-full w-full rounded-full object-cover object-center"
              src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
              alt=""
            />
            <span class="absolute right-0 bottom-0 h-2 w-2 rounded-full bg-green-400 ring ring-white"></span>
          </div>
          <div class="text-sm">
            <div class="font-medium text-gray-700">Steven Jobs</div>
            <div class="text-gray-400">jobs@sailboatui.com</div>
          </div>
        </th>
        <td class="px-6 py-4">
          <span
            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600"
          >
            <span class="h-1.5 w-1.5 rounded-full"></span>
            nom1
          </span>
        </td>
        <td class="px-6 py-4">nom2</td>
        <td class="px-6 py-4">
          <div class="flex gap-2">
            <span
              class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600"
            >
              nom3
            </span>

          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div> --}}

@php
    use Carbon\Carbon;
    use Livewire\Component;
    use App\Models\Presence;
    use Filament\Support\Colors\Color;
    use Filament\Notifications\Notification;

    $latestPresenceForThisUser = Presence::where('user_id', auth()->user()->id)
        ->where('created_at', today())
        ->first();
@endphp

<div>
    @if ($latestPresenceForThisUser && $latestPresenceForThisUser->heure_depart == null)
        <x-filament::button  color="danger" wire:click="departure">
            Enregistrer son départ
        </x-filament::button>
    @else
        <x-filament::button wire:click="arrival">
            Enregistrer son arrivée
        </x-filament::button>
    @endif

</div>

<?php

namespace App\Livewire;

use App\Models\PickupSlot;
use Livewire\Component;

class PickupPointSelector extends Component
{
    // ID du point de retrait sélectionné
    public $selectedPickupSlotId;

    // Date de retrait sélectionnée
    public $pickupDate;

    // Horaire sélectionné (pour affichage)
    public $selectedTimeSlot;

    // Liste des créneaux horaires disponibles pour la date sélectionnée
    public $availableTimeSlots = [];

    protected $rules = [
        'selectedPickupSlotId' => 'required|exists:pickup_slots,id',
        'pickupDate' => 'required|date|after_or_equal:today',
        'selectedTimeSlot' => 'required',
    ];

    protected $messages = [
        'selectedPickupSlotId.required' => 'Veuillez sélectionner un point de retrait sur la carte.',
        'pickupDate.required' => 'Veuillez sélectionner une date de retrait.',
        'pickupDate.after_or_equal' => 'La date de retrait doit être aujourd\'hui ou dans le futur.',
        'selectedTimeSlot.required' => 'Veuillez sélectionner un horaire de retrait.',
    ];

    public function mount()
    {
        // Initialise avec la date du jour + 1
        $this->pickupDate = now()->addDay()->format('Y-m-d');
    }

    /**
     * Appelé quand l'utilisateur sélectionne un point de retrait sur la carte
     */
    public function selectPickupPoint($pickupSlotId)
    {
        $this->selectedPickupSlotId = $pickupSlotId;
        $this->updateAvailableTimeSlots();
    }

    /**
     * Appelé quand l'utilisateur change la date de retrait
     */
    public function updatedPickupDate()
    {
        $this->updateAvailableTimeSlots();
        $this->selectedTimeSlot = null; // Reset du créneau horaire
    }

    /**
     * Met à jour les créneaux horaires disponibles en fonction du point et de la date
     */
    public function updateAvailableTimeSlots()
    {
        $this->availableTimeSlots = [];

        if (!$this->selectedPickupSlotId || !$this->pickupDate) {
            return;
        }

        $pickupSlot = PickupSlot::find($this->selectedPickupSlotId);

        if (!$pickupSlot || empty($pickupSlot->working_hours)) {
            return;
        }

        // Récupère le jour de la semaine (lundi = monday, etc.)
        $dayOfWeek = strtolower(now()->parse($this->pickupDate)->locale('en')->dayName);

        // Cherche les horaires pour ce jour
        $daySchedule = collect($pickupSlot->working_hours)->firstWhere('day', $dayOfWeek);

        if ($daySchedule && (!isset($daySchedule['closed']) || !$daySchedule['closed'])) {
            $this->availableTimeSlots = [
                [
                    'value' => $daySchedule['open'] . '-' . $daySchedule['close'],
                    'label' => substr($daySchedule['open'], 0, 5) . ' - ' . substr($daySchedule['close'], 0, 5),
                ]
            ];
        }
    }

    /**
     * Récupère tous les points de retrait actifs avec leurs coordonnées
     */
    public function getPickupPointsProperty()
    {
        return PickupSlot::where('is_active', true)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'name' => $slot->name,
                    'address' => $slot->address,
                    'lat' => (float) $slot->lat,
                    'lng' => (float) $slot->lng,
                    'working_hours' => $this->formatWorkingHours($slot->working_hours ?? []),
                ];
            });
    }

    /**
     * Formate les horaires pour l'affichage dans la popup
     */
    private function formatWorkingHours(array $hours): string
    {
        if (empty($hours)) {
            return 'Horaires non définis';
        }

        $formatted = collect($hours)->map(function ($schedule) {
            $dayName = match($schedule['day']) {
                'monday' => 'Lun',
                'tuesday' => 'Mar',
                'wednesday' => 'Mer',
                'thursday' => 'Jeu',
                'friday' => 'Ven',
                'saturday' => 'Sam',
                'sunday' => 'Dim',
                default => $schedule['day']
            };

            if (isset($schedule['closed']) && $schedule['closed']) {
                return $dayName . ': Fermé';
            }

            return $dayName . ': ' . substr($schedule['open'], 0, 5) . '-' . substr($schedule['close'], 0, 5);
        })->join('<br>');

        return $formatted;
    }

    public function render()
    {
        return view('livewire.pickup-point-selector', [
            'pickupPoints' => $this->pickupPoints,
        ]);
    }
}

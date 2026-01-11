<?php

namespace App\Livewire;

use App\Models\Bundle;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSlot;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CheckoutForm extends Component
{
    // Informations client
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $notes;

    // Informations de retrait (depuis PickupPointSelector)
    public $selectedPickupSlotId;
    public $pickupDate;
    public $selectedTimeSlot;
    public $availableTimeSlots = [];
    public $availableDays = []; // Jours de la semaine disponibles pour Flatpickr

    // Panier
    public $cart = [];
    public $total = 0;

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => ['required', 'string', 'regex:/^(\+262|0262|0)\s?[0-9]{3}\s?[0-9]{2}\s?[0-9]{2}\s?[0-9]{2}$/'],
        'selectedPickupSlotId' => 'required|exists:pickup_slots,id',
        'pickupDate' => 'required|date|after_or_equal:today',
        'selectedTimeSlot' => 'required',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'customer_name.required' => 'Le nom complet est obligatoire.',
        'customer_email.required' => 'L\'email est obligatoire.',
        'customer_email.email' => 'L\'email doit être valide.',
        'customer_phone.required' => 'Le téléphone est obligatoire.',
        'customer_phone.regex' => 'Le format du téléphone n\'est pas valide. Ex: +262 692 XX XX XX ou 0692 XX XX XX',
        'selectedPickupSlotId.required' => 'Veuillez sélectionner un point de retrait sur la carte.',
        'pickupDate.required' => 'La date de retrait est obligatoire.',
        'pickupDate.after_or_equal' => 'La date de retrait doit être aujourd\'hui ou dans le futur.',
        'selectedTimeSlot.required' => 'Veuillez sélectionner un horaire de retrait.',
    ];

    public function mount()
    {
        // Récupère le panier depuis la session
        $this->cart = session()->get('cart', []);

        // Redirige si le panier est vide
        if (empty($this->cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide.');
        }

        // Calcule le total
        $this->calculateTotal();

        // Pré-remplit avec les données de l'utilisateur connecté
        $user = auth()->user();
        if ($user) {
            $this->customer_name = $user->name;
            $this->customer_email = $user->email;
            $this->customer_phone = $user->phone ?? '';
        }

        // Initialise avec la date du jour + 1
        $this->pickupDate = now()->addDay()->format('Y-m-d');
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price_cents'] * $item['quantity'];
        }
    }

    /**
     * Appelé quand l'utilisateur sélectionne un point de retrait sur la carte
     */
    public function selectPickupPoint($pickupSlotId)
    {
        $this->selectedPickupSlotId = $pickupSlotId;
        $this->updateAvailableDays(); // Met à jour les jours disponibles pour Flatpickr
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

        // Cherche TOUS les horaires pour ce jour (peut avoir plusieurs créneaux)
        $daySchedules = collect($pickupSlot->working_hours)->filter(function($schedule) use ($dayOfWeek) {
            return $schedule['day'] === $dayOfWeek && (!isset($schedule['closed']) || !$schedule['closed']);
        });

        // Crée un créneau pour chaque horaire trouvé
        foreach ($daySchedules as $schedule) {
            $this->availableTimeSlots[] = [
                'value' => $schedule['open'] . '-' . $schedule['close'],
                'label' => substr($schedule['open'], 0, 5) . ' - ' . substr($schedule['close'], 0, 5),
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
     * Met à jour les jours de la semaine disponibles pour Flatpickr
     */
    public function updateAvailableDays()
    {
        $this->availableDays = [];

        if (!$this->selectedPickupSlotId) {
            return; // Tous les jours sont sélectionnables si pas de point choisi
        }

        $pickupSlot = PickupSlot::find($this->selectedPickupSlotId);

        if (!$pickupSlot || empty($pickupSlot->working_hours)) {
            return;
        }

        // Récupère les jours où le point est ouvert (pas fermé)
        $this->availableDays = collect($pickupSlot->working_hours)
            ->filter(function ($schedule) {
                return !isset($schedule['closed']) || !$schedule['closed'];
            })
            ->pluck('day')
            ->unique()
            ->map(function ($day) {
                // Convertit en numéro de jour (0 = dimanche, 1 = lundi, ..., 6 = samedi)
                return match($day) {
                    'sunday' => 0,
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                    default => null
                };
            })
            ->filter()
            ->values()
            ->toArray();
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

    /**
     * Soumet la commande
     */
    public function submitOrder()
    {
        // Validation
        $this->validate();

        try {
            DB::beginTransaction();

            // Vérifier le stock avant de créer la commande
            foreach ($this->cart as $item) {
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if (!$product) {
                        throw new \Exception("Le produit '{$item['name']}' n'existe plus.");
                    }
                    if (!$product->isAvailable($item['quantity'])) {
                        throw new \Exception($product->getStockErrorMessage());
                    }
                }

                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::with('products')->find($item['id']);
                    if (!$bundle) {
                        throw new \Exception("Le panier '{$item['name']}' n'existe plus.");
                    }
                    if (!$bundle->isAvailable($item['quantity'])) {
                        throw new \Exception($bundle->getStockErrorMessage($item['quantity']));
                    }
                }
            }

            // Créer la commande
            $order = Order::create([
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'total_price_cents' => $this->total,
                'pickup_date' => $this->pickupDate,
                'pickup_slot_id' => $this->selectedPickupSlotId,
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            // Créer les items et réserver le stock
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'item_id' => $item['id'],
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $item['price_cents'],
                    'total_price_cents' => $item['price_cents'] * $item['quantity'],
                ]);

                // Réserver le stock pour les produits
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if ($product) {
                        $product->decrementStock($item['quantity']);
                    }
                }

                // Réserver le stock pour les paniers
                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::with('products')->find($item['id']);
                    if ($bundle) {
                        foreach ($bundle->products as $product) {
                            $quantityNeeded = $product->pivot->quantity_included * $item['quantity'];
                            $product->decrementStock($quantityNeeded);
                        }
                    }
                }
            }

            DB::commit();

            // Vider le panier
            session()->forget('cart');

            // Redirection vers la page de confirmation
            return redirect()->route('checkout.confirmation', $order)->with('success', 'Commande créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de commande: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            session()->flash('error', 'Une erreur est survenue lors de la création de la commande: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout-form', [
            'pickupPoints' => $this->pickupPoints,
        ]);
    }
}

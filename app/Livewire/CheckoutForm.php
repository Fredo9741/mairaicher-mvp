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
        \Log::info('=== DÉBUT SUBMITORDER ===');
        \Log::info('Données du formulaire:', [
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'pickup_slot_id' => $this->selectedPickupSlotId,
            'pickup_date' => $this->pickupDate,
            'selected_time_slot' => $this->selectedTimeSlot,
            'cart_items_count' => count($this->cart),
            'total' => $this->total,
        ]);

        try {
            \Log::info('Début de la validation');
            // Validation
            $this->validate();
            \Log::info('Validation réussie');

            \Log::info('Début de la transaction DB');
            DB::beginTransaction();

            // Vérifier le stock avant de créer la commande
            \Log::info('Vérification du stock pour ' . count($this->cart) . ' articles');
            foreach ($this->cart as $index => $item) {
                \Log::info("Vérification article #{$index}: {$item['name']} (type: {$item['type']})");

                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if (!$product) {
                        throw new \Exception("Le produit '{$item['name']}' n'existe plus.");
                    }
                    \Log::info("Produit trouvé: {$product->name}, stock: {$product->stock_quantity}");

                    if (!$product->isAvailable($item['quantity'])) {
                        throw new \Exception($product->getStockErrorMessage());
                    }
                }

                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::find($item['id']);
                    if (!$bundle) {
                        throw new \Exception("Le panier '{$item['name']}' n'existe plus.");
                    }
                    \Log::info("Panier trouvé: {$bundle->name}, stock disponible: {$bundle->quantity}");

                    if (!$bundle->isAvailable($item['quantity'])) {
                        throw new \Exception($bundle->getStockErrorMessage($item['quantity']));
                    }
                }
            }
            \Log::info('Vérification du stock terminée avec succès');

            // Créer la commande
            \Log::info('Création de la commande dans la DB');
            $orderData = [
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'total_price_cents' => $this->total,
                'pickup_date' => $this->pickupDate,
                'pickup_time_slot' => $this->selectedTimeSlot,
                'pickup_slot_id' => $this->selectedPickupSlotId,
                'status' => 'pending',
                'notes' => $this->notes,
            ];
            \Log::info('Données de la commande:', $orderData);

            $order = Order::create($orderData);
            \Log::info("Commande créée avec succès: ID={$order->id}, Numéro={$order->order_number}");

            // Créer les items et réserver le stock
            \Log::info('Création des OrderItems');
            foreach ($this->cart as $index => $item) {
                \Log::info("Création OrderItem #{$index} pour article: {$item['name']}");

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
                        \Log::info("Décrémentation du stock pour: {$product->name}, quantité: {$item['quantity']}");
                        $product->decrementStock($item['quantity']);
                    }
                }

                // Réserver le stock pour les paniers
                if ($item['type'] === 'bundle') {
                    $bundle = Bundle::find($item['id']);
                    if ($bundle) {
                        \Log::info("Décrémentation du stock pour le panier: {$bundle->name}, quantité: {$item['quantity']}");
                        $bundle->decrement('quantity', $item['quantity']);
                    }
                }
            }
            \Log::info('Tous les OrderItems créés avec succès');

            \Log::info('Commit de la transaction');
            DB::commit();
            \Log::info('Transaction commitée avec succès');

            // Vider le panier
            \Log::info('Vidage du panier de la session');
            session()->forget('cart');

            // Vérifier que la route existe
            \Log::info('Vérification de la route checkout.confirmation');
            if (!route('checkout.confirmation', ['order' => $order->id], false)) {
                throw new \Exception('La route checkout.confirmation n\'existe pas');
            }

            // Redirection vers la page de confirmation
            \Log::info("Redirection vers checkout.confirmation avec order_id={$order->id}");
            \Log::info('=== FIN SUBMITORDER (SUCCÈS) ===');

            return redirect()->route('checkout.confirmation', $order)->with('success', 'Commande créée avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('=== ERREUR DE VALIDATION ===');
            \Log::error('Erreurs de validation:', $e->errors());
            \Log::error($e->getTraceAsString());

            session()->flash('error', 'Erreur de validation: ' . json_encode($e->errors()));

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('=== ERREUR DE BASE DE DONNÉES ===');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('SQL: ' . $e->getSql());
            \Log::error('Bindings: ' . json_encode($e->getBindings()));
            \Log::error($e->getTraceAsString());

            session()->flash('error', 'Erreur de base de données: ' . $e->getMessage());

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('=== ERREUR GÉNÉRALE ===');
            \Log::error('Type: ' . get_class($e));
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('Fichier: ' . $e->getFile() . ' (ligne ' . $e->getLine() . ')');
            \Log::error('Stack trace:');
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

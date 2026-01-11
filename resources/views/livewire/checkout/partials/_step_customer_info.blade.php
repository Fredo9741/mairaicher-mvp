{{-- Étape 1 : Informations client --}}
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    {{-- En-tête de l'étape --}}
    <div
        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition"
        @click="currentStep === 1 ? null : (currentStep = 1)"
        :class="{'bg-green-50': stepCompleted[1], 'border-l-4 border-green-600': currentStep === 1}"
    >
        <div class="flex items-center gap-4">
            {{-- Indicateur d'étape --}}
            <div
                class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-bold"
                :class="{
                    'bg-green-600 text-white border-green-600': stepCompleted[1],
                    'bg-blue-600 text-white border-blue-600': currentStep === 1 && !stepCompleted[1],
                    'bg-gray-100 text-gray-400 border-gray-300': currentStep !== 1 && !stepCompleted[1]
                }"
            >
                <span x-show="!stepCompleted[1]">1</span>
                <svg x-show="stepCompleted[1]" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">Vos informations</h2>
                <p class="text-sm text-gray-600" x-show="stepCompleted[1]">
                    {{ $customer_name ?? 'Informations complétées' }}
                </p>
            </div>
        </div>

        {{-- Icône expand/collapse --}}
        <svg
            class="w-6 h-6 text-gray-400 transition-transform"
            :class="{'rotate-180': currentStep === 1}"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    {{-- Contenu de l'étape --}}
    <div
        x-show="currentStep === 1"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="p-6 border-t border-gray-200"
    >
        <div class="space-y-4">
            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet <span class="text-red-600">*</span>
                </label>
                <input
                    type="text"
                    id="customer_name"
                    wire:model.blur="customer_name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_name') border-red-500 @enderror"
                    placeholder="Jean Dupont"
                >
                @error('customer_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-600">*</span>
                </label>
                <input
                    type="email"
                    id="customer_email"
                    wire:model.blur="customer_email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_email') border-red-500 @enderror"
                    placeholder="jean.dupont@example.com"
                >
                @error('customer_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Téléphone <span class="text-red-600">*</span>
                </label>
                <input
                    type="tel"
                    id="customer_phone"
                    wire:model.blur="customer_phone"
                    placeholder="+262 692 XX XX XX"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 @error('customer_phone') border-red-500 @enderror"
                >
                @error('customer_phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes (optionnel)
                </label>
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Informations complémentaires..."></textarea>
            </div>
        </div>

        {{-- Bouton suivant --}}
        <div class="mt-6 flex justify-end">
            <button
                type="button"
                @click="validateStep1()"
                class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium"
            >
                Continuer
                <svg class="inline w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    ownerType: String,
    ownerId: String,
    existingQuoteId: {
        type: String,
        default: null
    },
    existingState: {
        type: Object,
        default: () => ({})
    },
    manifest: {
        type: Object,
        required: true
    },
    apiEndpoint: {
        type: String,
        default: '/cpq/quotes'
    }
});

// Local state
const activeQuoteId = ref(props.existingQuoteId);
const selectedOfferingIds = ref(props.existingState?.selected_offerings || []);
const isSaving = ref(false);

const runningTotal = computed(() => {
    return selectedOfferingIds.value.reduce((total, id) => {
        const item = props.manifest.offerings[id];
        return total + (item ? item.price_cents : 0);
    }, 0);
});

const saveQuote = async () => {
    isSaving.value = true;
    
    try {
        const response = await fetch(props.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                // Grab the CSRF token from Laravel's standard meta tag
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                quote_id: activeQuoteId.value,
                owner_type: props.ownerType,
                owner_id: props.ownerId,
                configurator_state: {
                    selected_offerings: selectedOfferingIds.value
                }
            })
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        
        if (data.success) {
            activeQuoteId.value = data.quote.id;
            alert('Draft saved successfully!');
        }
    } catch (error) {
        console.error('Error saving quote:', error);
        alert('Failed to save quote draft.');
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <!-- Template remains exactly the same, but update the button to show saving state -->
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow mt-10">
        <h1 class="text-2xl font-bold mb-6">Quote Selector</h1>

        <div v-for="tab in manifest.presentation.staff_selector" :key="tab.tab_name" class="mb-8">
            <h2 class="text-xl font-semibold border-b pb-2 mb-4">{{ tab.tab_name }}</h2>
            
            <div class="space-y-3">
                <div v-for="itemId in tab.item_ids" :key="itemId" class="flex items-center justify-between">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            :value="itemId" 
                            v-model="selectedOfferingIds"
                            class="w-5 h-5 rounded border-gray-300 text-blue-600"
                        >
                        <span class="text-gray-800">{{ manifest.offerings[itemId].name }}</span>
                    </label>

                    <span class="text-gray-600">
                        ${{ (manifest.offerings[itemId].price_cents / 100).toFixed(2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t flex justify-between items-center bg-gray-50 p-4 rounded">
            <div class="text-lg font-medium text-gray-700">
                Running Total: <span class="font-bold text-black">${{ (runningTotal / 100).toFixed(2) }}</span>
            </div>
            <button 
                @click="saveQuote" 
                :disabled="isSaving"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50"
            >
                {{ isSaving ? 'Saving...' : 'Save Quote Draft' }}
            </button>
        </div>
    </div>
</template>
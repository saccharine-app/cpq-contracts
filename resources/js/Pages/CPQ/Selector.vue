<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

// Receive the manifest JSON from the Laravel controller
const props = defineProps({
    manifest: {
        type: Object,
        required: true
    }
});

// STATE: An array holding the IDs of the checkboxes the user clicks
const selectedOfferingIds = ref([]);

// COMPUTED: Reactively calculates the total price based on the selected IDs
const runningTotal = computed(() => {
    return selectedOfferingIds.value.reduce((total, id) => {
        const item = props.manifest.offerings[id];
        return total + (item ? item.price_cents : 0);
    }, 0);
});

// ACTION: Submit the state back to Laravel to save the quote
const saveQuote = () => {
    router.post('/cpq/quotes', {
        configurator_state: {
            selected_offerings: selectedOfferingIds.value
        }
    });
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Quote Selector MVP</h1>

        <!-- The Presentation Loop -->
        <div v-for="tab in manifest.presentation.staff_selector" :key="tab.tab_name" class="mb-8">
            <h2 class="text-xl font-semibold border-b pb-2 mb-4">{{ tab.tab_name }}</h2>
            
            <div class="space-y-3">
                <!-- The Items Loop -->
                <div v-for="itemId in tab.item_ids" :key="itemId" class="flex items-center justify-between">
                    
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <!-- V-model automatically adds/removes the ID from the selectedOfferingIds array -->
                        <input 
                            type="checkbox" 
                            :value="itemId" 
                            v-model="selectedOfferingIds"
                            class="w-5 h-5 rounded border-gray-300 text-blue-600"
                        >
                        <!-- O(1) Lookup against the flat dictionary -->
                        <span class="text-gray-800">{{ manifest.offerings[itemId].name }}</span>
                    </label>

                    <span class="text-gray-600">
                        ${{ (manifest.offerings[itemId].price_cents / 100).toFixed(2) }}
                    </span>
                    
                </div>
            </div>
        </div>

        <!-- The Footer / Total -->
        <div class="mt-8 pt-4 border-t flex justify-between items-center bg-gray-50 p-4 rounded">
            <div class="text-lg font-medium text-gray-700">
                Running Total: <span class="font-bold text-black">${{ (runningTotal / 100).toFixed(2) }}</span>
            </div>
            
            <button @click="saveQuote" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Save Quote Draft
            </button>
        </div>
    </div>
</template>
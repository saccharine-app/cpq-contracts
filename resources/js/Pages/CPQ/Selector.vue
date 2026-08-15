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

// State
const activeQuoteId = ref(props.existingQuoteId);
const selectedOfferingIds = ref(props.existingState?.selected_offerings || []);
const isSaving = ref(false);

// Tab Navigation State: Default to the first available tab
const tabs = computed(() => props.manifest.presentation?.staff_selector || []);
const activeTab = ref(tabs.value[0]?.tab_name || '');

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
    <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mt-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Quote Builder</h1>

        <!-- Tab Navigation Bar -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-6">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.tab_name"
                    @click="activeTab = tab.tab_name"
                    type="button"
                    :class="[
                        activeTab === tab.tab_name
                            ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-semibold'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 font-medium',
                        'whitespace-nowrap py-3 px-1 border-b-2 text-sm transition-colors'
                    ]"
                >
                    {{ tab.tab_name }}
                </button>
            </nav>
        </div>

        <!-- Active Tab Content -->
        <div v-for="tab in tabs" :key="tab.tab_name" v-show="activeTab === tab.tab_name" class="space-y-4 mb-8 min-h-[16rem]">
            <div 
                v-for="itemId in tab.item_ids" 
                :key="itemId" 
                class="flex items-center justify-between p-3 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
            >
                <label class="flex items-center space-x-3 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        :value="itemId" 
                        v-model="selectedOfferingIds"
                        class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                    >
                    <span class="text-gray-800 dark:text-gray-200">{{ manifest.offerings[itemId]?.name }}</span>
                </label>

                <span class="text-gray-600 dark:text-gray-300 font-medium">
                    ${{ ((manifest.offerings[itemId]?.price_cents || 0) / 100).toFixed(2) }}
                </span>
            </div>
        </div>

        <!-- Footer / Sticky Totals Bar -->
        <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg">
            <div class="text-lg font-medium text-gray-700 dark:text-gray-300">
                Running Total: <span class="font-bold text-gray-900 dark:text-white">${{ (runningTotal / 100).toFixed(2) }}</span>
            </div>
            
            <button 
                @click="saveQuote" 
                :disabled="isSaving"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 font-medium"
            >
                {{ isSaving ? 'Saving Draft...' : 'Save Quote Draft' }}
            </button>
        </div>
    </div>
</template>
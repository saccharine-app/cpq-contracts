<script setup>
// Workbench placeholder for the CPQ Selector page.
//
// The real page ships as a publishable Vue artifact (see
// `vendor:publish --tag=cpq-views`); this stub exists so Inertia's testing
// helpers can resolve the `CPQ/Selector` component name inside Workbench,
// where no host application assets are compiled.
defineProps({
    ownerType: String,
    ownerId: String,
    existingQuoteId: [String, null],
    existingState: [Object, null],
    manifest: Object,
});
</script>

<template>
    <div data-testid="cpq-selector-placeholder">
        <pre>{{ manifest }}</pre>
    </div>
</template>

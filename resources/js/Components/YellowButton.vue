<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-yellow-300 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-yellow-200 focus:bg-yellow-100 active:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-100 focus:ring-offset-2 transition ease-in-out duration-150">
        <slot/>
    </button>
</template>

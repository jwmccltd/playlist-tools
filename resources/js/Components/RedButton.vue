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
        class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>

text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300

<script setup>
import DataLoader from '@/Components/DataLoader.vue';
</script>

<template>
    <div class="flex justify-center">
        <DataLoader :loading-text="'finding your playlists'" :data-route="route('spotify.get-data', 'playlists')"/>
    </div>
</template>
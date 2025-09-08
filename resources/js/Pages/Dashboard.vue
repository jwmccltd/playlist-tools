<script setup>
import LayoutBase from '@/Layouts/LayoutBase.vue';
import LayoutContain from '@/Layouts/LayoutContain.vue';
import MyPlaylists from '@/Components/MyPlaylists.vue';
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    playlistsWithConfigs: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <Head title="Dashboard"/>

    <LayoutBase>
        <template #layout>
            <LayoutContain>
                <template #logo>
                    <ApplicationLogo/>
                </template>

                <template #content>
                    <MyPlaylists class="main-content" :playlists-with-configs/>
                </template>
            </LayoutContain>
        </template>
    </LayoutBase>
</template>

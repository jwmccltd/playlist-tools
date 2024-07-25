<script setup>
import Layout from '@/Layouts/Layout.vue';
import MyPlaylists from '@/Components/MyPlaylists.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <Layout>
        <MyPlaylists/>
    </Layout>
</template>

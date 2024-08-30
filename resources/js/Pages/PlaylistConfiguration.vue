<script setup>
import LayoutBase from '@/Layouts/LayoutBase.vue';
import LayoutFull from '@/Layouts/LayoutFull.vue';
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head title="Dashboard" />

    <LayoutBase>
        <template #layout>
            <LayoutFull>
                <template #logo>
                    <ApplicationLogo/>
                </template>

                <template #content>
                    Hello
                </template>    
            </LayoutFull>
        </template>
    </LayoutBase>
</template>

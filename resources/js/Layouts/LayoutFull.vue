<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="">
        <div>
            <Link href="/">
                <slot name="logo"/>
            </Link>
        </div>

        <div class="main-content-full">
            <slot name="content"/>
        </div>
    </div>
</template>

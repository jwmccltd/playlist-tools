<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="w-full">
        <div>
            <Link href="/">
                <slot name="logo"/>
            </Link>
        </div>

        <div class="main-content-wide">
            <slot name="content"/>
        </div>
    </div>
</template>

<script setup>
import LayoutBase          from '@/Layouts/LayoutBase.vue';
import Layout              from '@/Layouts/Layout.vue';
import PrimaryButton       from '@/Components/PrimaryButton.vue';
import { faSpotify }       from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    link: {
        type: String
    },
});

const spotifyConnect = () => {
    window.location.href = props.link;
};
</script>

<template>
    <Head title="Connect" />
    <LayoutBase>
        <template #layout>
            <Layout>
                <template #logo>
                    <ApplicationLogo/>
                </template>
                <template #content>
                    <div class="flex justify-center p-4">
                        <PrimaryButton @click="spotifyConnect()">
                            <span>CONNECT WITH SPOTIFY</span><font-awesome-icon :icon="faSpotify" class="ml-2 text-lg" />
                        </PrimaryButton>
                    </div>
                </template>
            </Layout>
         </template>
    </LayoutBase>
</template>

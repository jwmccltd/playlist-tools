<script setup>
import Layout              from '@/Layouts/Layout.vue';
import PrimaryButton       from '@/Components/PrimaryButton.vue';
import { faSpotify }       from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

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

    <Layout>
        <div class="flex justify-center">
            <PrimaryButton @click="spotifyConnect()">
                <span>CONNECT WITH SPOTIFY</span><font-awesome-icon :icon="faSpotify" class="ml-2 text-lg" />
            </PrimaryButton>
        </div>
    </Layout>
</template>

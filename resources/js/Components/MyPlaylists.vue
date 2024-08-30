<script setup>
import DataLoader from '@/Components/DataLoader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { faGear } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const playlists = ref([]);

/**
 * Process playlists using returned data.
 * 
 * @param {object} data The spotify data. 
 * @returns {void}
 */
const processPlaylists = (data) => {
    playlists.value = data;
}

</script>

<template>
    <div>
        <div class="flex justify-center">
            <DataLoader :loading-text="'finding your playlists'" :data-route="route('spotify.get-data', ['playlists', 'playlists'])" @dataReturned="processPlaylists"/>
        </div>

        <div v-if="playlists.length > 0" class="flex flex-wrap justify-center">
            <div v-for="playlist of playlists" :key="playlist.id" class="p-4">
                <div class="flex flex-col">
                    <div class="mb-2 text-center">
                        <span class="text-lg font-bold">{{ playlist.name }}</span>
                    </div>
                    <div>
                        <img :src="playlist.images[0].url" class="playlist-tile" />
                    </div>
                    <div class="flex justify-center p-4">
                        <Link :href="route('spotify-playlist.index', { 'playlistId' : playlist.id })">
                            <PrimaryButton>
                                <span>CONFIGURE</span><font-awesome-icon :icon="faGear" class="ml-2 text-lg" />
                            </PrimaryButton>
                        </Link>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
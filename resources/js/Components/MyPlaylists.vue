<script setup>
import DataLoader from '@/Components/DataLoader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { faGear } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    playlistsWithConfigs: {
        type: Object,
        required: true,
    },
});

const playlists = ref([]);

/**
 * Process playlists using returned data.
 *
 * @param {object} data The spotify data.
 * @returns {void}
 */
const processPlaylists = (data) => {
    playlists.value = data;
};

const configHasActive = (playlistId) => {
    if (
        typeof props.playlistsWithConfigs[playlistId] !== 'undefined'
        && props.playlistsWithConfigs[playlistId].active_count > 0
    ) {
        return true;
    }
    return false;
};

const configExists = (playlistId) => {
    if (
        typeof props.playlistsWithConfigs[playlistId] !== 'undefined'
    ) {
        return true;
    }
    return false;
};

const configHasInactive = (playlistId) => {
    if (
        typeof props.playlistsWithConfigs[playlistId] !== 'undefined'
        && props.playlistsWithConfigs[playlistId].inactive_count > 0
    ) {
        return true;
    }
    return false;
};

</script>

<template>
    <div>
        <div class="flex justify-center">
            <DataLoader @data-returned="processPlaylists" :loading-text="'finding your playlists'" :data-route="route('spotify.get-data', ['playlists', ['me','playlists']])"/>
        </div>
        <div v-if="playlists.length > 0" class="flex items-end flex-wrap justify-center">
            <div v-for="playlist of playlists" :key="playlist.id" class="p-4">
                <div class="flex flex-col max-w-52">
                    <div class="mb-2 text-center">
                        <span class="text-lg font-bold text-wrap">{{ playlist.name }}</span>
                    </div>
                    <div v-if="playlist.images !== null">
                        <img :src="playlist.images[0].url" class="playlist-tile"/>
                    </div>
                    <div v-else>
                        <img src="/images/no-playlist-image.png" class="playlist-tile"/>
                    </div>
                    <div class="flex justify-center p-4">
                        <Link :href="route('spotify-playlist.index', { 'playlistLinkId' : playlist.id })">
                            <PrimaryButton>
                                <span :class="{ 'text-yellow-300': configHasActive(playlist.id) }">
                                    CONFIGURE
                                </span>
                                <font-awesome-icon :icon="faGear" class="ml-2 text-lg" :class="{ 'text-yellow-300' : configHasActive(playlist.id) }"/>

                                <div v-if="configExists(playlist.id)" class="ml-2 flex items-center w-full justify-center fill-current text-gray-500">
                                    <div class="letter-tiles">
                                        <span v-if="configHasActive(playlist.id)" class="small yellow" title="Active Config">
                                            {{ props.playlistsWithConfigs[playlist.id].active_count }}
                                        </span>
                                        <span v-if="configHasInactive(playlist.id)" class="small ml-2" title="Inactive Config">
                                            {{ props.playlistsWithConfigs[playlist.id].inactive_count }}
                                        </span>
                                    </div>
                                </div>
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

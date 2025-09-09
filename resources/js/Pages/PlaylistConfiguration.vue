<script setup>
import LayoutBasePositionTop from '@/Layouts/LayoutBasePositionTop.vue';
import LayoutWide from '@/Layouts/LayoutWide.vue';
import { Head } from '@inertiajs/vue3';
import { ref, provide, watch, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import YellowButton from '@/Components/YellowButton.vue';
import { faPlay } from '@fortawesome/free-solid-svg-icons';
import { faPeopleGroup } from '@fortawesome/free-solid-svg-icons';
import { faCompactDisc } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import PlaylistConfig from '@/Components/PlaylistConfigs/PlaylistConfig.vue';
import PlaylistConfigInfo from '@/Components/PlaylistConfigs/Info/PlaylistConfigInfo.vue';
import PlaylistConfigScheduler from '@/Components/PlaylistConfigs/PlaylistConfigScheduler.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import draggable from 'vuedraggable';

const props = defineProps({
    playlistLinkId: {
        type: String,
        required: true,
    },
    playlistName: {
        type: String,
        required: true,
    },
    playlistImageUrl: {
        type: String,
        required: true,
    },
    playlistDescription: {
        type: String,
        required: true,
    },
    playlistFollowers: {
        type: String,
        required: true,
    },
    playlistTrackTotal: {
        type: String,
        required: true,
    },
    playlistArtists: {
        type: Object,
        required: true,
    },
    playlists: {
        type: Object,
        required: true,
    },
    playlistTracks: {
        type: Object,
        required: true,
    },
    playlistConfigurations: {
        type: Object,
        required: true,
    },
    playlistConfigurationSchedule: {
        type: Object,
        default: () => {
            return {};
        },
    },
});

provide('playlistArtists', props.playlistArtists);
provide('playlists', props.playlists);
provide('playlistTracks', props.playlistTracks);

const stringChars = (stringObject) => {
    const returnArray = [];
    for (var i = 0; i < stringObject.length; i++) {
        returnArray.push(stringObject.charAt(i));
    }
    return returnArray;
};

const configs = ref([]);
const errors  = ref({});

const saveConfig = (config, isGlobal) => {
    errors.value[config.itemId] = {};

    if (isGlobal !== true) {
        config.config.active = 1;
    }
    axios.post(route('spotify-playlist.store'), {
        playlistLinkId: props.playlistLinkId,
        configOptionId: config.fields.option_id,
        ... config.config,
    }).then(function(response) {
        config.config.id = response.data;
    }).catch(function (error) {
        setTimeout(() => {
            if (isGlobal !== true) {
                config.config.active = 0;
                refreshActiveConfigs();
            }
        }, 500);
        errors.value[config.itemId] = error.response.data.errors;
    });
};

const executeConfig = () => {
    axios.post(route('spotify-playlist.execute-config', { playlistLinkId: props.playlistLinkId }));
};

const updateStepOrder = () => {

    const configOrderIds = [];
    let step = 1;
    moveableConfigs.value.forEach((config) => {
        configOrderIds.push(config.id);
        config.config.step = step;
        step++;
    });

    if (configOrderIds.length > 0) {
        axios.post(route('spotify-playlist.update-step-order', { playlistLinkId: props.playlistLinkId }),
            {
                configOptionIds: configOrderIds,
            });
    }

};

const configActive = (event, config) => {
    if (event === true) {
        saveConfig(config);
    } else {
        axios.post(route('spotify-playlist.update-active-state', { playlistLinkId: props.playlistLinkId, optionId: config.fields.option_id, state: 0 }));
        config.config.active = 0;
    }
    refreshActiveConfigs();
};

configs.value = props.playlistConfigurations;

const moveableConfigs = ref([]);
const globalConfigs = ref([]);

const activeConfigs = ref([]);
const drag = ref(false);;

globalConfigs.value = configs.value.filter((config) => {
    return config.is_global === 1;
});

watch(globalConfigs.value, (newGlobal) => {
    saveConfig(newGlobal[0], true);
});

moveableConfigs.value = configs.value.filter((config) => {
    return config.is_global !== 1;
});

const refreshActiveConfigs = () => {
    activeConfigs.value = configs.value.filter((config) => {
        return config.config.active === 1 && config.is_global === 0;
    });
};

refreshActiveConfigs();

const hasActiveConfigs = computed(() =>
    activeConfigs.value.length > 0);

</script>

<template>
    <Head title="Playlist Configuration"/>

    <LayoutBasePositionTop class="pb-8">
        <template #layout>
            <LayoutWide>
                <template #logo>
                    <ApplicationLogo/>
                </template>

                <template #content>
                    <div class="grid grid-cols-2">
                        <div class="grid grid-cols-2">
                            <div class="p-4">
                                <div class="flex flex-col">
                                    <div class="mb-2 text-center">
                                        <span class="text-lg font-bold">{{ playlistName }}</span>
                                    </div>
                                    <div>
                                        <img :src="playlistImageUrl" class="w-full"/>
                                        <div class="flex flex-row items-center mt-2">
                                            <div class="p-2">
                                                <font-awesome-icon :icon="faPeopleGroup" size="xl" class="emerald"/>
                                            </div>
                                            <div class="p-2">
                                                <div class="flex items-center w-full justify-center fill-current text-gray-500">
                                                    <div class="letter-tiles">
                                                        <span v-for="(stringChar, index) of stringChars(playlistFollowers)" :key="index" class="small">
                                                            {{ stringChar }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-2">
                                                <font-awesome-icon :icon="faCompactDisc" size="xl" class="cyan"/>
                                            </div>
                                            <div class="p-2">
                                                <div class="flex items-center w-full justify-center fill-current text-gray-500">
                                                    <div class="letter-tiles">
                                                        <span v-for="(stringChar, index) of stringChars(playlistTrackTotal)" :key="index" class="small">
                                                            {{ stringChar }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <YellowButton v-if="hasActiveConfigs" @click="executeConfig()" class="mt-4">
                                            <span>RUN CONFIG ONCE</span><font-awesome-icon :icon="faPlay" class="ml-2"/>
                                        </YellowButton>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 flex flex-col">
                                <PlaylistConfigScheduler :playlistLinkId :playlistConfigurationSchedule/>
                            </div>
                        </div>
                        <div class="p-4">
                            <iframe
                                data-testid="embed-iframe"
                                style="border-radius:12px"
                                :src="`https://open.spotify.com/embed/playlist/${playlistLinkId}?utm_source=generator`"
                                width="100%"
                                height="500"
                                frameBorder="0"
                                allowfullscreen=""
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                loading="lazy"/>
                        </div>
                    </div>
                </template>
            </LayoutWide>
            <div class="w-5/6">
                <div class="letter-tiles mt-8">
                    <span class="medium cyan">
                        T
                    </span>
                    <span v-for="(letter, index) of ['O','O','L','S']" :key="index" class="medium">
                        {{ letter }}
                    </span>
                </div>
                <div class="mb-8">
                    <div v-for="(config, index) of globalConfigs" :key="index">
                        <div class="flex flex-wrap items-center justify-center main-content-full global-config-background relative">
                            <div class="absolute left-4 top-4 text-center">
                                <div>
                                    <span class="letter-tiles">
                                        <span class="emerald small">
                                            GLOBAL
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <PlaylistConfig
                                v-model="config.config"
                                :component-name="config.component"
                                :fields="config.fields.config_fields"
                                :errors="errors"/>
                        </div>
                    </div>
                </div>

                <draggable
                    @start="drag=true"
                    @end="drag=false"
                    @change="updateStepOrder()"
                    v-model="moveableConfigs"
                    item-key="id">
                    <template #item="{element}">
                        <div class="flex flex-wrap items-center justify-start main-content-full active-config-background relative">
                            <div class="absolute left-4 top-4 text-center">
                                <div>
                                    <span class="letter-tiles">
                                        <span class="emerald small">
                                            {{ element.config.step }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <PlaylistConfig
                                v-if="element.config.active === 0"
                                v-model="element.config"
                                :fields="element.fields.config_fields"
                                :item-id="element.item_id"
                                :component-name="element.component"
                                :errors="errors"/>
                            <PlaylistConfigInfo
                                v-if="element.config.active === 1"
                                :config="element.config"
                                :component-name="element.component"
                                :fields="element.fields.config_fields"/>
                            <ToggleSwitch
                                @check-box-on="configActive($event, element)"
                                :control-switch="true"
                                :checked="element.config.active === 1"
                                title="activate / deactivate"
                                class="ml-auto"/>
                        </div>
                    </template>
                </draggable>
            </div>
        </template>
    </LayoutBasePositionTop>
</template>

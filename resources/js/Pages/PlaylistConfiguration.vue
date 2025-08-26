<script setup>
import LayoutBasePositionTop from '@/Layouts/LayoutBasePositionTop.vue';
import LayoutFull from '@/Layouts/LayoutFull.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, provide } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import RedButton from '@/Components/RedButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { faPlay } from '@fortawesome/free-solid-svg-icons';
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import { faPeopleGroup } from '@fortawesome/free-solid-svg-icons';
import { faCompactDisc } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import PlaylistConfig from '@/Components/PlaylistConfigs/PlaylistConfig.vue';
import PlaylistConfigInfo from '@/Components/PlaylistConfigs/Info/PlaylistConfigInfo.vue';
import Arrow from '@/Components/Symbols/Arrow.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import draggable from 'vuedraggable';

const props = defineProps({
    playlistLinkId: {
        type: String,
    },
    playlistName: {
        type: String,
    },
    playlistImageUrl: {
        type: String,
    },
    playlistDescription: {
        type: String,
    },
    playlistFollowers: {
        type: String,
    },
    playlistTrackTotal: {
        type: String,
    },
    playlistConfigOptions: {
        type: Array
    },
    playlistArtists: {
        type: Object
    },
    playlists: {
        type: Object
    },
    playlistTracks: {
        type: Object
    },
    playlistConfigurations: {
        type: Object
    },
    playlistConfigurationOptionFields: {
        type: Object,
    }
});

provide('playlistArtists', props.playlistArtists);
provide('playlists', props.playlists);
provide('playlistTracks', props.playlistTracks);

const getComponentFields = (optionId) => {
    const configFields = props.playlistConfigurationOptionFields.filter((fields) => {
        return fields.option_id === optionId;
    });

    return configFields[0].config_fields;
};

const stringChars = (stringObject) => {
    const returnArray = [];
    for (var i = 0; i < stringObject.length; i++) {
        returnArray.push(stringObject.charAt(i));
    }
    return returnArray;
};

const configs = ref([]);
const errors  = ref({});

const addNewConfig = () => {
    configs.value.push({ itemId: configs.value.length + 1, model: {}, configComponent: null });
};

const deleteConfig = (configId) => {
    router.post(route('spotify-playlist.delete', { configId: configId, playlistLinkId: props.playlistLinkId }));
};

const removeConfig = (config) => {
    configs.value = configs.value.filter(function (conf) {
        return conf.itemId !== config.itemId;
    });
};

const saveConfig = (config) => {
    errors.value[config.itemId] = {};
    axios.post(route('spotify-playlist.store'), {
        playlistLinkId: props.playlistLinkId,
        configOptionId: config.configComponent === null ? null : config.configComponent.id,
        ... config.model,
    }).then(function (response) {
        config.id = response.data;
    }).catch(function (error) {
        errors.value[config.itemId] = error.response.data.errors;
    });
};

const updateConfig = (config) => {
    errors.value[config.itemId] = {};

    axios.post(route('spotify-playlist.update'), {
        configId: config.id,
        configOptionId: config.configComponent.id,
        ... config.model,
    }).then(function (response) {

    }).catch(function (error) {
        errors.value[config.itemId] = error.response.data.errors;
    });
};

const executeConfig = () => {
    axios.post(route('spotify-playlist.execute-config', { playlistLinkId: props.playlistLinkId })).then(function (response) {
        console.log(response);
    }).catch(function (error) {
        console.log(error);
    });
};

const updateStepOrder = () => {

    const configOrderIds = [];
    let step = 1;
    activeConfigs.value.forEach((config) => {
        configOrderIds.push(config.id);
        config.step = step;
        step++;
    });

    if (configOrderIds.length > 0) {
        axios.post(route('spotify-playlist.update-step-order', { ids: configOrderIds }));
    }

};

const refreshActiveConfigs = () => {
    activeConfigs.value = configs.value.filter((config) => {
        return config.active === 1;
    });
    updateStepOrder();
};

const configActive = (event, config) => {
    if (event === true) {
        config.active = 1;
        refreshActiveConfigs();
    } else {
        config.active = 0;
        refreshActiveConfigs();
    }

    axios.post(route('spotify-playlist.update-active-state', { configId: config.id, state: config.active }));
};

configs.value = props.playlistConfigurations;

const activeConfigs = ref([]);
const drag = ref(false);

refreshActiveConfigs();

activeConfigs.value = configs.value.filter((config) => {
    return config.active === 1;
});

const hasDraftConfig = () => {
    return configs.value.length !== activeConfigs.value.length;
};

</script>

<template>
    <Head title="Playlist Configuration"/>

    <LayoutBasePositionTop>
        <template #layout>
            <LayoutFull>
                <template #logo>
                    <ApplicationLogo/>
                </template>

                <template #content>
                    <div class="flex flex-wrap">
                        <div class="p-4">
                            <div class="flex flex-col">
                                <div class="mb-2 text-center">
                                    <span class="text-lg font-bold">{{ playlistName }}</span>
                                </div>
                                <div>
                                    <img :src="playlistImageUrl" class="playlist-tile"/>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 flex flex-col">
                            <p class="mb-2"><strong>{{ playlistDescription }}</strong></p>
                            <div class="flex flex-row items-center">
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
                            </div>
                            <div class="flex flex-row items-center">
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
                            <div class="mt-auto ml-auto">
                                <PrimaryButton @click.prevent="addNewConfig">
                                    <span>ADD NEW CONFIG</span><font-awesome-icon :icon="faPlus" class="ml-2"/>
                                </PrimaryButton>
                                <RedButton v-if="activeConfigs.length > 0" @click="executeConfig()" class="ml-2">
                                    <span>EXECUTE CONFIG</span><font-awesome-icon :icon="faPlay" class="ml-2"/>
                                </RedButton>
                            </div>
                        </div>
                    </div>
                </template>
            </LayoutFull>
            <div v-if="activeConfigs.length > 0" class="mt-8 pl-3 pr-3 mb-8 w-full">
                <div class="letter-tiles">
                    <span v-for="(letter, index) of ['A','C','T','I','V','E']" :key="index" class="medium">
                        {{ letter }}
                    </span>
                </div>

                <draggable
                    @start="drag=true"
                    @end="drag=false"
                    @change="updateStepOrder()"
                    v-model="activeConfigs"
                    item-key="id">
                    <template #item="{element}">
                        <div class="flex flex-wrap items-center justify-center main-content-full active-config-background relative">
                            <div class="absolute left-4 top-4 text-center">
                                <div>
                                    <span class="letter-tiles">
                                        <span class="emerald small">
                                            {{ element.step }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <PlaylistConfigInfo :config="element" :fields="getComponentFields(element.configComponent.id)"/>
                            <ToggleSwitch
                                v-if="element.id"
                                @check-box-on="configActive($event, element)"
                                :control-switch="true"
                                :checked="element.active === 1"
                                title="activate / deactivate"
                                class="ml-2"/>
                        </div>
                    </template>
                </draggable>
            </div>
            <div v-if="configs.length > 0 && hasDraftConfig()" class="mt-8 pl-3 pr-3 mb-8 w-full">
                <div class="letter-tiles">
                    <span v-for="(letter, index) of ['D','R','A','F','T']" :key="index" class="medium">
                        {{ letter }}
                    </span>
                </div>
                <div class="mb-8">
                    <div v-for="(config, index) of configs" :key="index">
                        <div v-if="!config.active" class="flex flex-wrap items-center justify-center main-content-full" :class="{ 'update-config-background' : config.id }">
                            <div>
                                <div class="panel bg-white">
                                    <select v-model="config.configComponent" class="emerald border text-sm rounded-lg block w-full p-2.5">
                                        <option value="null">Select Option</option>
                                        <option
                                            v-for="playlistConfigOption of playlistConfigOptions"
                                            :key="playlistConfigOption.id"
                                            :value="{ id: playlistConfigOption.id, component: playlistConfigOption.component }">
                                            {{ playlistConfigOption.name }}
                                        </option>
                                    </select>
                                    <div v-if="typeof errors[config.itemId] !== 'undefined' && errors[config.itemId]['configOptionId']" class="mt-2 text-red-600">
                                        <p class="text-center">{{ errors[config.itemId]['configOptionId'][0] }}</p>
                                    </div>
                                </div>
                            </div>
                            <Arrow/>

                            <PlaylistConfig
                                v-if="config.configComponent !== null"
                                v-model="config.model"
                                :fields="getComponentFields(config.configComponent.id)"
                                :option-id="config.configComponent.id"
                                :item-id="config.itemId"
                                :errors="errors"/>
                            <SecondaryButton v-if="!config.id" @click="saveConfig(config)">Save Config</SecondaryButton>
                            <SecondaryButton v-else @click="updateConfig(config)">Update Config</SecondaryButton>
                            <RedButton v-if="!config.id" @click="removeConfig(config)" class="ml-2">Remove Config</RedButton>
                            <RedButton v-else @click="deleteConfig(config)" class="ml-2">Delete Config</RedButton>
                            <ToggleSwitch v-if="config.id" @check-box-on="configActive($event, config)" :control-switch="true" :checked="config.active === 1" title="activate / deactivate" class="ml-2"/>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </LayoutBasePositionTop>
</template>

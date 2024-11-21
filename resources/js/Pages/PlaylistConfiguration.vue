<script setup>
import LayoutBase from '@/Layouts/LayoutBase.vue';
import LayoutFull from '@/Layouts/LayoutFull.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { faPlus } from '@fortawesome/free-solid-svg-icons';
import { faPeopleGroup } from '@fortawesome/free-solid-svg-icons';
import { faCompactDisc } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    playlistId: {
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
    }
});

const stringChars = (stringObject) => {
    const returnArray = [];
    for (var i = 0; i < stringObject.length; i++) {
        returnArray.push(stringObject.charAt(i))
    }
    return returnArray;
};

const configs = ref([]);

const addNewConfig = () => {
    configs.value.push({});
}

</script>

<template>
    <Head title="Playist Configuration" />

    <LayoutBase>
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
                                    <img :src="playlistImageUrl" class="playlist-tile" />
                                </div>
                            </div>
                        </div>
                        <div class="p-4 flex flex-col">
                            <p class="mb-2"><strong>{{ playlistDescription }}</strong></p>
                            <div class="flex flex-row items-center">
                                <div class="p-2">
                                    <font-awesome-icon :icon="faPeopleGroup" size="xl" class="emerald" />
                                </div>
                                <div class="p-2">
                                    <div class="flex items-center w-full justify-center fill-current text-gray-500">
                                        <div class="letter-tiles">
                                            <span class="small" v-for="(stringChar, index) of stringChars(playlistFollowers)" :key="index">
                                               {{ stringChar }}
                                            </span>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row items-center">
                                <div class="p-2">
                                    <font-awesome-icon :icon="faCompactDisc" size="xl" class="cyan" />
                                </div>
                                <div class="p-2">
                                    <div class="flex items-center w-full justify-center fill-current text-gray-500">
                                        <div class="letter-tiles">
                                            <span class="small" v-for="(stringChar, index) of stringChars(playlistTrackTotal)" :key="index">
                                               {{ stringChar }}
                                            </span>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="mt-auto ml-auto">
                                <PrimaryButton @click.prevent="addNewConfig">
                                    <span>ADD NEW CONFIG</span><font-awesome-icon :icon="faPlus" class="ml-2" />
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </template>    
            </LayoutFull>
            <LayoutFull>
                <template #content>
                    <div v-for="(config, index) of configs" :key="index">
                        <div>
                            Hello
                        </div>
                    </div>
                </template>    
            </LayoutFull>
        </template>
    </LayoutBase>
</template>

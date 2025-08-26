<script setup>

import { ref, inject, watch } from 'vue';
import Plus from '@/Components/Symbols/Plus.vue';
import Arrow from '@/Components/Symbols/Arrow.vue';
import ModalSelect from '@/Components/FieldComponents/ModalSelect.vue';

const configModel = defineModel();

const props = defineProps({
    errors: {
        type: Object,
    },
    itemId: {
        type: Number,
    },
    optionId: {
        type: Number,
    },
    fields: {
        type: Object,
    },
});

const playlistArtists = inject('playlistArtists');
const playlists       = inject('playlists');
const playlistTracks  = inject('playlistTracks');

const getDataSource = function (dataSourceString) {
    return eval(dataSourceString);
};

const configFields = ref(props.fields);

for (const [ident, data] of Object.entries(configFields.value)) {
    if (data.default && (typeof configModel.value[ident] === 'undefined' || configModel.value[ident] === null)) {
        configModel.value[ident] = data.default;
    }
};

// Watch for changes in props.optionId.
watch(() => props.fields, () => {
    configFields.value = props.fields;
});

</script>
<template>
    <template v-for="(field, ident) of configFields" :key="ident">
        <div v-if="field.type === 'number'" class="panel bg-white m-2">
            <p>{{ field.label }}<span><input v-model="configModel[ident]" class="mx-1.5 w-24" type="number"></span></p>
            <div v-if="typeof errors[itemId] !== 'undefined' && errors[itemId][ident]" class="mt-2 text-red-600">
                <p class="text-center">{{ errors[itemId][ident][0] }}</p>
            </div>
        </div>

        <div v-else-if="field.type === 'dropdown'" class="panel bg-white m-2">
            <div class="flex flex-row items-center">
                <div>
                    <p>{{ field.label }}</p>
                </div>
                <div class="mx-1.5">
                    <select v-model="configModel[ident]" class="emerald border text-sm rounded-lg block w-full p-2.5">
                        <option v-for="option of field.options" :key="option.value" :value="option.value" :selected="option.value === field.default">
                            {{ option.text }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div v-else-if="field.type === 'modal-select'">
            <ModalSelect
                v-model="configModel[ident]"
                :title="field.label"
                :button-label="field.buttonLabel"
                :modal-title="field.modalTitle"
                :data="getDataSource(field.dataSource)"
                :option-display="field.optionDisplay ?? []"/>
        </div>

        <Arrow v-if="field.arrow === 1"/>
        <Plus v-if="field.plus === 1"/>
    </template>
</template>

<script setup>
import { ref, watch } from 'vue';

const emit = defineEmits(['checkBoxOn']);

defineProps({
    controlSwitch: {
        type: Boolean,
        default: false,
    },
    value: {
        type: String,
        required: false,
    },
    checked: {
        type: Boolean,
        default: false,
    },
});

const selectedElements = defineModel();

const emitState = (event) => {
    emit('checkBoxOn', event.target.checked);
};

</script>

<template>
    <div v-if="controlSwitch === true">
        <label class="switch">
            <input @change="emitState($event)" type="checkbox" :checked="checked"/>
            <span class="slider round"></span>
        </label>
    </div>
    <div v-else>
        <label class="switch">
            <input v-model="selectedElements" type="checkbox" :value="value"/>
            <span class="slider round"></span>
        </label>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const emit = defineEmits(['checkBoxOn']);

const props = defineProps({
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
    }
});

const selectedElements = defineModel();

const emitState = (event) => {
    emit('checkBoxOn', event.target.checked);
};

</script>

<template>
    <div v-if="controlSwitch === true">
        <label class="switch">
            <input type="checkbox" @change="emitState($event)" :checked="checked" />
            <span class="slider round"></span>
        </label>
    </div>
    <div v-else>
        <label class="switch">
            <input type="checkbox" :value="value" v-model="selectedElements" />
            <span class="slider round"></span>
        </label>
    </div>
</template>

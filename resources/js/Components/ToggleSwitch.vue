<script setup>
const emit = defineEmits(['checkBoxOn']);

defineProps({
    controlSwitch: {
        type: Boolean,
        default: false,
    },
    value: {
        type: String,
        required: false,
        default: null,
    },
    checked: {
        type: Boolean,
        default: false,
    },
    activeColourClass: {
        type: String,
        required: false,
        default: null,
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
            <span class="slider round" :class="`${activeColourClass}`"/>
        </label>
    </div>
    <div v-else>
        <label class="switch">
            <input v-model="selectedElements" type="checkbox" :value="value"/>
            <span class="slider round" :class="`${activeColourClass}`"/>
        </label>
    </div>
</template>

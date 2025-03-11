<script setup>
import { ref, watch } from 'vue';
import { selectAllToggle } from '@/utils/selectAll.js'

const props = defineProps({
    control: {
        type: String,
        default: '',
    },
    ident: {
        type: String,
        default: ''
    },
});

const elementChecked = defineModel();

const targetElements = (control) => {
    selectAllToggle.ident = control;
    // Click handler is fired before element state is updated.
    selectAllToggle.state = !elementChecked.value;
};

watch(selectAllToggle, (newState) => {
  if (newState.ident === props.ident) {
    elementChecked.value = newState.state;
  }  
});

const shouldCheck = () => {
    if (ident === selectAllToggle.ident && selectAllToggle.state === true) {
        return true;
    }
    return false;
}

</script>

<template>
    <div v-if="control !== ''">
        <label class="switch">
            <input type="checkbox" v-model="elementChecked" @click="targetElements(control)" />
            <span class="slider round"></span>
        </label>
    </div>
    <div v-else>
        <label class="switch">
            <input type="checkbox" v-model="elementChecked" />
            <span class="slider round"></span>
        </label>
    </div>
</template>

<style>

</style>

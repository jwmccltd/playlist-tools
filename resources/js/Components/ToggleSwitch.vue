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
        default: '',
    },
    id: {
        type: String,
        required: false,
    },
    value: {
        type: String,
        required: false,
    },
    data: {
        type: Object,
    }
});

const selectedElements = defineModel();

const checked = ref(false);

const targetElements = (event, control) => {
    selectAllToggle.state = event.target.checked;
    selectAllToggle.ident = control;
};

watch(selectAllToggle, (newState) => {
  if (newState.ident === props.ident) {
    if (selectAllToggle.state === true) {
        checked.value = true;
    } else {
        checked.value = false;
    }
  }  

  console.log(selectedElements.value);
});

</script>

<template>
    <div v-if="control !== ''">
        <label class="switch">
            <input type="checkbox" @change="targetElements($event, control)" />
            <span class="slider round"></span>
        </label>
    </div>
    <div v-else>
        <label class="switch">
            <input type="checkbox" :value="value" :checked="checked" @click="checked = !checked" v-model="selectedElements" />
            <span class="slider round"></span>
        </label>
    </div>
</template>

<style>

</style>

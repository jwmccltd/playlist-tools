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
    }
});

const selectedElements = defineModel();

const checkAll = ref(false);

const targetElements = (event, control) => {
    selectAllToggle.state = event.target.checked;
    selectAllToggle.ident = control;
};

const addRemoveElement = (value, isRemoving) => {
    const remove = isRemoving || false;

    if (remove === false) {
        selectedElements.value.push(value);
        selectedElements.value = [...new Set(selectedElements.value)];
    } else {
        const index = selectedElements.value.indexOf(value);
        if (index > -1) {
            selectedElements.value.splice(index, 1);
        }
    }
}

const checkElement = (event) => {
    if (event.target.checked === true) {
        addRemoveElement(props.value);
    } else {
        addRemoveElement(props.value, true);
    }
};

watch(selectAllToggle, (newState) => {
  if (newState.ident === props.ident) {
    if (selectAllToggle.state === true) {
        checkAll.value = true;
        addRemoveElement(props.value);
    } else {
        checkAll.value = false;
        selectedElements.value = [];
    }
  }  
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
            <input type="checkbox" :value="id" :checked="checkAll" @change="checkElement($event)"/>
            <span class="slider round"></span>
        </label>
    </div>
</template>

<style>

</style>

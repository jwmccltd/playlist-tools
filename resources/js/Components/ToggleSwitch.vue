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
        selectedElements.push(value);
        selectedElements = [...new Set(selectedElements)];
    } else {
        const index = selectedElements.indexOf(value);
        if (index > -1) {
            selectedElements.splice(index, 1);
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

const shouldCheck = (id) => {
    if (checkAll.value === true) {
        return true;
    }

    for (let elementId of selectedElements) {
        if (elementId === id) {
            return true;
        }
    }

    return false;
}

watch(selectAllToggle, (newState) => {
  if (newState.ident === props.ident) {
    if (selectAllToggle.state === true) {
        checkAll.value = true;
        addRemoveElement(props.value);
    } else {
        checkAll.value = false;
        selectedElements = [];
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
            <input type="checkbox" :value="id" :checked="shouldCheck(id)" @change="checkElement($event)"/>
            <span class="slider round"></span>
        </label>
    </div>
</template>

<style>

</style>

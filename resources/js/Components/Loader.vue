<script setup>
import { faCompactDisc, faSpinner } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon }          from '@fortawesome/vue-fontawesome';

defineProps({
    loadingText: {
        type: String,
        default: 'Loading',
    },
    loadingState: {
        type: Boolean,
        default: false
    },
    finishedText: {
        type: String,
        required: false,
        default: null,
    }
});

</script>

<template>
    <div class="flex content-center place-items-center loading" v-if="loadingState === true">
        <div class="text-lg">{{ loadingText }}<span>.</span><span>.</span><span>.</span></div> <font-awesome-icon :icon="faCompactDisc" class="ml-2" size="2x" spin/>
    </div>    
    <div v-else-if="finishedText !== null" class="flex content-center place-items-center">
        <div class="text-lg">{{ finishedText }}<span>.</span><span>.</span><span>.</span></div> <div><font-awesome-icon :icon="faCompactDisc" class="ml-2" size="2x"/></div>    
    </div>
</template>
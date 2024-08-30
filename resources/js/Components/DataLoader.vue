<script setup>
import Loader from '@/Components/Loader.vue';
import { ref, defineEmits } from 'vue';

const emit = defineEmits(['dataReturned'])

const props = defineProps({
    loadingText: {
        type: String,
        default: 'Loading',
    },
    dataRoute: {
        type: String,
        required: true,
    },
    finishedText: {
        type: String,
        required: false,
        default: null,
    }
});

const loading = ref(false);
const onCompleteText = ref(props.finishedText);

const loadData = function () {
    loading.value = true;
    axios.get(props.dataRoute)
    .then(function (response) {
        // handle success
        if (typeof response.data.error !== 'undefined') {
            console.log('error!');
        }

        loading.value = false;
        if (props.finishedText === null) {
            onCompleteText.value = 'found ' + response.data.length + ' results';
        }

        emit('dataReturned', response.data);
    });
};

loadData();

</script>

<template>
    <div>
        <Loader :loading-text="loadingText" :loading-state="loading" :finished-text="onCompleteText" />
    </div>
</template>
        
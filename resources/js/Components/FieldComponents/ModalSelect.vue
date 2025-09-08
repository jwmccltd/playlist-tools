<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import { faTimes } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    modalTitle: {
        type: String,
        required: true,
    },
    buttonLabel: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    optionDisplay: {
        type: Array,
        required: false,
        default: () => {
            return [];
        },
    },
});

const selectedElements = defineModel();

if (typeof selectedElements.value === 'undefined') {
    selectedElements.value = [];
}

const openSelect = ref(false);

const closeSelectModal = () => {
    openSelect.value = false;
};

const resetSelected = () => {
    selectedElements.value = [];
};

const isChecked = (checked) => {
    if (checked === true) {
        selectedElements.value = Object.keys(props.data);
    } else {
        resetSelected();
    }
};

const showCount = () => {
    return typeof selectedElements.value !== 'undefined' ? selectedElements.value.length : 0;
};

const shouldCheckAll = computed(() => {
    return Object.keys(props.data).length === selectedElements.value.length;
});

</script>
<template>
    <div>
        <div class="panel bg-white m-2 tool-panel">
            <div class="flex flex-row items-center">
                <div><p>{{ title }}</p></div>
                <div>
                    <span>
                        <PrimaryButton @click.prevent="openSelect = true" class="ml-2">{{ buttonLabel }}</PrimaryButton>
                    </span>
                </div>
                <div v-if="showCount() > 0" class="letter-tiles ml-2">
                    <span class="cyan small">{{ showCount() }}</span>
                </div>
            </div>
        </div>

        <Modal @close="closeSelectModal" :show="openSelect">
            <div class="p-6">
                <div @click="closeSelectModal" class="flex justify-end">
                    <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
                </div>
                <div class="flex flex-row">
                    <div>
                        <div class="text-2xl mb-4 modal-label-field">{{ modalTitle }}</div>
                    </div>
                    <div>
                        <ToggleSwitch @check-box-on="isChecked" :control-switch="true" :checked="shouldCheckAll"/>
                    </div>
                </div>
                <div class="modal-scroll-height h-full overflow-y-auto">
                    <div v-for="(item, index) of data" :key="index">
                        <div class="flex flex-row items-center mb-2">
                            <div v-if="optionDisplay.length === 0" class="modal-label-field">
                                <strong>{{ item }}</strong>
                            </div>
                            <div v-else class="modal-label-field">
                                <strong>{{ item[optionDisplay[0]] }}</strong>
                                <br>
                                <small v-if="optionDisplay[1] !== null">
                                    {{ item[optionDisplay[1]] }}
                                </small>
                            </div>
                            <div>
                                <ToggleSwitch
                                    v-model="selectedElements"
                                    :value="index"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-between">
                    <PrimaryButton @click.prevent="resetSelected">Reset</PrimaryButton>
                    <SecondaryButton @click="closeSelectModal">Ok</SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>

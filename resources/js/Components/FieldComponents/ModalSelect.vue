<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import { faPlus, faTimes } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    title: {
        type: String,
    },
    modalTitle: {
         type: String,
    },
    buttonLabel: {
        type: String,
    },
    data: {
        type: Object,
    },
    optionDisplay: {
         type: Array,
         required: false,
         default: [],
    }
});

const selectedElements = defineModel();

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
}

const showCount = () => {
   return typeof selectedElements.value !== 'undefined' ? selectedElements.value.length : 0;
};

const shouldCheckAll = computed(() => {
   return Object.keys(props.data).length === selectedElements.value.length
});

const elementToggleId = ref(props.title.toLowerCase().replaceAll(' ',''));

</script>
<template>
    <div class="panel bg-slate-200 m-2">
      <div class="flex flex-row items-center">
         <div><p>{{ title }}</p></div>
         <div>
            <span>
               <PrimaryButton @click.prevent="openSelect = true" class="ml-2">{{ buttonLabel }}</PrimaryButton>
            </span>
         </div>
         <div class="letter-tiles ml-2" v-if="showCount() > 0">
            <span class="cyan small">{{ showCount() }}</span>
         </div>
      </div>
   </div>

   <Modal :show="openSelect" @close="closeSelectModal">
      <div class="p-6">
         <div class="flex justify-end" @click="closeSelectModal">
            <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
         </div>
         <div class="flex flex-row">
            <div>
               <div class="text-2xl mb-4 modal-label-field">{{ modalTitle }}</div>
            </div>
            <div>
               <ToggleSwitch :control-switch="true" @checkBoxOn="isChecked" :checked="shouldCheckAll"></ToggleSwitch>
            </div>
         </div>
         <div class="modal-scroll-height h-full overflow-y-auto">
            <div v-for="(item, index) of data" :key="index">
               <div class="flex flex-row items-center mb-2">
                  <div class="modal-label-field" v-if="optionDisplay.length === 0">
                     <strong>{{ item }}</strong>
                  </div>
                  <div v-else class="modal-label-field">
                     <strong>{{ item[optionDisplay[0]] }}</strong>
                     <br/>
                     <small v-if="optionDisplay[1] !== null">
                        {{ item[optionDisplay[1]] }}
                     </small>
                  </div>
                  <div>
                     <ToggleSwitch
                        v-model="selectedElements"
                        :value="index">
                     </ToggleSwitch>
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
</template>

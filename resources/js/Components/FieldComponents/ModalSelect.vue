<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { ref } from 'vue';
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
    }
});    

const selectedElements = defineModel();

//console.log('Elements');
//console.log(selectedElements);

const openSelect = ref(false);

const closeSelectModal = () => {
   openSelect.value = false;
};

const resetSelected = () => {
   selectedElements = [];
};

const elementToggleId = ref(props.title.toLowerCase().replaceAll(' ',''));

//console.log(elementToggleId);

</script>
<template>
    <div class="panel bg-slate-200 m-2">
      <p>{{ title }}
         <span>
            <PrimaryButton @click.prevent="openSelect = true" class="ml-2">{{ buttonLabel }}</PrimaryButton>
         </span>
      </p>
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
               <ToggleSwitch :control="elementToggleId"></ToggleSwitch>
            </div>
         </div>
         <div class="modal-scroll-height h-full overflow-y-auto">
            <div v-for="(item, index) of data" :key="index">
               <div class="flex flex-row items-center mb-2">
                  <div class="modal-label-field">{{ item }}</div>
                  <div>
                     <ToggleSwitch 
                        :ident="elementToggleId"
                        v-model="selectedElements"
                        :value="index"
                        :id="index">
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

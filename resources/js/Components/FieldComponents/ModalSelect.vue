<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';

const props = defineProps({
    title: {
        type: String,
    },
    buttonLabel: {
        type: String,
    },
    data: {
        type: Object,
    }
});    

const excludeArtistSelect = ref(false);

const closeArtistSelectModal = () => {
   excludeArtistSelect.value = false;
};


</script>
<template>
    <div class="panel bg-slate-200 m-2">
      <p>{{ title }}
         <span>
            <PrimaryButton @click.prevent="excludeArtistSelect = true" class="ml-2">{{ buttonLabel }}</PrimaryButton>
         </span>
      </p>
   </div>
   <div class="mx-2">
      <div class="panel-small">
         <font-awesome-icon :icon="faPlus" size="xl" class="text-slate-500"/>
      </div> 
    </div>

    <Modal :show="excludeArtistSelect" @close="closeArtistSelectModal">
      <div class="p-6">
         <div class="flex justify-end" @click="closeArtistSelectModal">
            <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
         </div>
         <div class="flex flex-row">
            <div>
               <div class="text-2xl mb-4 modal-label-field">Filter Playlist Artists</div>
            </div>
            <div>
               <ToggleSwitch :control="'artist-select'"></ToggleSwitch>
            </div>
         </div>
         <div class="modal-scroll-height h-full overflow-y-auto">
            <div v-for="(artist, index) of data" :key="index">
               <div class="flex flex-row items-center mb-2">
                  <div class="modal-label-field">{{ artist }}</div>
                  <div>
                     <ToggleSwitch 
                        :ident="'artist-select'"
                        v-model="configModel.selectedArtists"
                        :value="index"
                        :id="index">
                     </ToggleSwitch>
                  </div>
               </div>
            </div>
         </div>   
         <div class="mt-6 flex justify-between">
            <PrimaryButton @click.prevent="resetSelectedArtists">Reset</PrimaryButton>
            <SecondaryButton @click="closeArtistSelectModal">Ok</SecondaryButton>
         </div>
      </div>
   </Modal>
</template>

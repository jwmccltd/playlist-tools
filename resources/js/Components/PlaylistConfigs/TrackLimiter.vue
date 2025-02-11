<script setup>

import { faArrowRight, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { inject } from 'vue'

const playlistArtists = inject('playlistArtists');
const playlists       = inject('playlists');

const excludeArtistSelect = ref(false);
const transferToSelect = ref(false);

const closeArtistSelectModal = () => {
   excludeArtistSelect.value = false;
};

const closeTransferModal = () => {
   transferToSelect.value = false;
};

const configModel = defineModel();
</script>
<template>
   <div class="panel m-2">
      <p>Limit track count to max<span><input class="mx-1.5 w-24" type="number"/>tracks</span></p>
   </div>

   <div class="m-2">
      <div class="panel-small">
         <font-awesome-icon :icon="faArrowRight" size="xl" class="cyan"/>
      </div> 
   </div> 
   <div class="panel m-2">
      <div class="flex flex-row items-center">
         <div>
            <p>By removing</p>
         </div>
         <div class="mx-1.5">
            <select class="emerald border text-sm rounded-lg block w-full p-2.5">
               <option value="default-end">From the end of default order</option>
               <option value="default-start">From the start of default order</option>
               <option value="oldest">Oldest tracks first</option>
               <option value="newest">Newest tracks first</option>
               <option value="random">Random</option>
            </select>
         </div>   
      </div> 
   </div>
   <div class="m-2">
      <div class="panel-small">
         <font-awesome-icon :icon="faPlus" size="xl" class="text-slate-500"/>
      </div>
   </div>

   <div class="panel bg-slate-200 m-2">
      <p>Exclude these artists from removal
         <span>
            <PrimaryButton @click.prevent="excludeArtistSelect = true" class="ml-2">Select Artists</PrimaryButton>
         </span>
      </p>
   </div>
   <div class="mx-2">
      <div class="panel-small">
         <font-awesome-icon :icon="faPlus" size="xl" class="text-slate-500"/>
      </div> 
   </div> 
   <div class="panel bg-slate-200 m-2">
      <p>Move removed to tracks to
         <span>
            <PrimaryButton @click.prevent="transferToSelect = true" class="ml-2">Select Playlists</PrimaryButton>
         </span>
      </p>
   </div>

   <Modal :show="excludeArtistSelect" @close="closeArtistSelectModal">
      <div class="p-6">
         <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
               <div class="text-2xl mb-4">Filter Playlist Artists</div>
            </div>
            <div>
               <ToggleSwitch :control="'artist-select'"></ToggleSwitch>
            </div>
         </div>
         <div v-for="(artist, index) of playlistArtists" :key="index">
            <div class="grid grid-cols-2 gap-4 mt-1 items-center">
               <div>{{ artist }}</div>
               <div>
                  <ToggleSwitch :ident="'artist-select'"></ToggleSwitch>
               </div>
            </div>
         </div>
         <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeArtistSelectModal"> Cancel </SecondaryButton>
         </div>
      </div>
   </Modal>

   <Modal :show="transferToSelect" @close="closeTransferModal">
      <div class="p-6">
         <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
               <div class="text-2xl mb-4">Filter Playlists</div>
            </div>
            <div>
               <ToggleSwitch :control="'playlist-select'"></ToggleSwitch>
            </div>
         </div>
         <div v-for="(playlist, index) of playlists" :key="index">
            <div class="grid grid-cols-2 gap-4 mt-1 items-center">
               <div>{{ playlist }}</div>
               <ToggleSwitch :ident="'playlist-select'"></ToggleSwitch>
            </div>
       
         </div>
         <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeTransferModal"> Cancel </SecondaryButton>
         </div>
      </div>
   </Modal>
</template>
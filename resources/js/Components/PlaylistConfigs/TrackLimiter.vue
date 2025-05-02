<script setup>

import ModalSelect from '@/Components/FieldComponents/ModalSelect.vue';
import { inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Plus from '@/Components/Symbols/Plus.vue';
import Arrow from '@/Components/Symbols/Arrow.vue';

const playlistArtists = inject('playlistArtists');
const playlists       = inject('playlists');
const playlistTracks  = inject('playlistTracks');

const page = usePage();

const configModel = defineModel();
configModel.value = { ...configModel.value, ...page.props.playlistConfigs.TrackLimiter };

</script>
<template>
   <div class="panel m-2">
      <p>Limit track count to max<span><input class="mx-1.5 w-24" type="number"/>tracks</span></p>
   </div>

   <Arrow/>
   <div class="panel m-2">
      <div class="flex flex-row items-center">
         <div>
            <p>By removing</p>
         </div>
         <div class="mx-1.5">
            <select class="emerald border text-sm rounded-lg block w-full p-2.5" v-model="configModel.byRemovingOption">
               <option value="default-end">From the end of default order</option>
               <option value="default-start">From the start of default order</option>
               <option value="oldest">Oldest tracks first</option>
               <option value="newest">Newest tracks first</option>
               <option value="random">Random</option>
            </select>
         </div>   
      </div> 
   </div>
   <Plus/>

   <ModalSelect 
      v-model="configModel.selectedArtists"
      :title="'Exclude these artists from removal'"
      :button-label="'Select Artists'"
      :modal-title="'Filter Playlist Artists'"
      :data="playlistArtists"
   />

   <Plus/>

   <ModalSelect 
      v-model="configModel.selectedTracks"
      :title="'Exclude these tracks from removal'"
      :button-label="'Select Tracks'"
      :modal-title="'Filter Tracks'"
      :data="playlistTracks"
      :option-display="['name','artists']"
   />

   <Plus/>

   <ModalSelect 
      v-model="configModel.selectedPlaylists"
      :title="'Move removed to tracks to'"
      :button-label="'Select Playlists'"
      :modal-title="'Filter Playlists'"
      :data="playlists"
   />

</template>
<script setup>

import { faArrowRight, faPlus, faTimes } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import { inject } from 'vue';
import { usePage } from '@inertiajs/vue3';

const playlistArtists = inject('playlistArtists');
const playlists       = inject('playlists');
const playlistTracks  = inject('playlistTracks');

const excludeArtistSelect = ref(false);
const excludeTrackSelect = ref(false);
const transferToSelect = ref(false);

const closeArtistSelectModal = () => {
   excludeArtistSelect.value = false;
};

const closeTrackSelectModal = () => {
   excludeTrackSelect.value = false;
}

const closeTransferPlaylistModal = () => {
   transferToSelect.value = false;
};

const resetSelectedArtists = () => {
   configModel.value.selectedArtists = [];
};

const page = usePage();

const configModel = defineModel();

configModel.value = page.props.playlistConfigs.TrackLimiter;

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
      <p>Exclude these tracks from removal
         <span>
            <PrimaryButton @click.prevent="excludeTrackSelect = true" class="ml-2">Select Tracks</PrimaryButton>
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
         <div class="flex justify-end" @click="closeArtistSelectModal">
            <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
         </div>
         <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
               <div class="text-2xl mb-4">Filter Playlist Artists</div>
            </div>
            <div>
               <ToggleSwitch :control="'artist-select'"></ToggleSwitch>
            </div>
         </div>
         <div class="modal-scroll-height h-full overflow-y-auto">
            <div v-for="(artist, index) of playlistArtists" :key="index">
               <div class="grid grid-cols-2 gap-4 mt-1 items-center">
                  <div>{{ artist }}</div>
                  <div class="toggle-indent">
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

   <Modal :show="excludeTrackSelect" @close="closeTrackSelectModal">
      <div class="p-6">
         <div class="flex justify-end" @click="closeTrackSelectModal">
            <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
         </div>
         <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
               <div class="text-2xl mb-4">Filter Tracks</div>
            </div>
            <div>
               <ToggleSwitch :control="'track-select'"></ToggleSwitch>
            </div>
         </div>
         <div v-for="(track, index) of playlistTracks" :key="index">
            <div class="grid grid-cols-2 gap-4 mt-1 items-start">
               <div>
                  <strong>{{ track.name }}</strong><br />
                  <small><i>{{ track.artists }}</i></small>
               </div>
               <div>
                  <ToggleSwitch 
                     :ident="'track-select'"
                     v-model="configModel.selectedTracks"
                     :value="index"
                     :id="index">
                  </ToggleSwitch>
               </div>
            </div>
       
         </div>
         <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeTrackSelectModal">Ok</SecondaryButton>
         </div>
      </div>
   </Modal>

   <Modal :show="transferToSelect" @close="closeTransferPlaylistModal">
      <div class="p-6">
         <div class="flex justify-end" @click="closeTransferPlaylistModal">
            <font-awesome-icon :icon="faTimes" size="xl" class="text-slate-500 cursor-pointer"/>
         </div>
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
               <ToggleSwitch 
                  :ident="'playlist-select'" 
                  v-model="configModel.selectedPlaylists"
                  :value="index"
                  :id="index">
               </ToggleSwitch>
            </div>
         </div>
         <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeTransferPlaylistModal">Ok</SecondaryButton>
         </div>
      </div>
   </Modal>
</template>
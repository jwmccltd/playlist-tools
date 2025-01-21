<script setup>

import { faArrowRight, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref } from 'vue';

const excludeArtistOption = ref(false);

</script>
<template>
   <div class="panel">
      <p>Limit track count to max<span><input class="mx-1.5 w-24" type="number"/>tracks</span></p>
   </div>

   <div class="mx-2">
      <div class="panel-small">
         <font-awesome-icon :icon="faArrowRight" size="xl" class="cyan"/>
      </div> 
   </div> 
   <div class="panel">
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

   <div class="flex flex-row items-center">
      <div class="mx-2">
         <div class="panel-small cursor-pointer">
            <font-awesome-icon :icon="faPlus" size="xl" class="text-slate-500"/>
         </div> 
      </div> 
      <div class="panel bg-slate-200">
         <p>Exclude these artists from removal<span><input class="mx-1.5 w-34"/></span></p>
      </div>
   </div>

   <div class="flex flex-row items-center mt-4">
      <div class="mx-2">
         <div class="panel-small cursor-pointer">
            <font-awesome-icon :icon="faPlus" size="xl" class="text-slate-500"/>
         </div> 
      </div> 
      <div class="panel bg-slate-200">
         <p>Move removed to tracks to<span><input class="mx-1.5 w-24" type="number"/></span></p>
      </div>
   </div>
</template>
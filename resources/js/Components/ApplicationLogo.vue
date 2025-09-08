<script setup>
import { faCompactDisc } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
</script>

<template>
    <div class="flex items-center h-28 w-full justify-center fill-current text-gray-500">
        <div class="letter-tiles">
            <span class="emerald">
                P
            </span>
            <span v-for="(letter, index) of ['L', 'A', 'Y', 'L', 'I', 'S', 'T']" :key="index">
                {{ letter }}
            </span>
        </div>
        <div>
            <font-awesome-icon :icon="faCompactDisc" size="3x" class="ml-4 mr-4 text-gray-800"/>
        </div>
        <div class="letter-tiles">
            <span class="cyan">
                T
            </span>
            <span v-for="(letter, index) of ['O','O','L','S']" :key="index">
                {{ letter }}
            </span>
        </div>
    </div>
</template>

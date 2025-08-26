<script setup>
const props = defineProps({
   config: {
        type: Object,
   },
   fields: {
        type: Object,
   }
});

</script>

<template>
    <div class="pl-8">
        <div class="panel bg-white">
            <strong>{{ config.component ?? config.configComponent.component }}</strong>
        </div>
    </div>
    <div v-for="(field, ident) of fields" :key="ident">
        <div v-if="field.type !== 'modal-select'" class="panel bg-white m-2">
            {{ field.label }} {{ props.config.model[ident] }}
        </div>
        <div v-else-if="props.config.model[ident].length > 0" class="panel bg-white m-2">
            {{ field.label }}
            <span class="letter-tiles">
                <span class="cyan small">
                    {{ props.config.model[ident].length }}
                </span>
            </span>
        </div>
    </div>
</template>

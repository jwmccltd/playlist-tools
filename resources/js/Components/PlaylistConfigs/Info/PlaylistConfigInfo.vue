<script setup>
const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
    fields: {
        type: Object,
        default: null,
    },
    componentName: {
        type: String,
        required: true,
    },
});

</script>

<template>
    <div class="pl-8">
        <div class="panel bg-white">
            <strong>{{ componentName }}</strong>
        </div>
    </div>
    <div v-for="(field, ident) of fields" :key="ident">
        <div v-if="field.type !== 'modal-select'" class="panel bg-white m-2">
            {{ field.label }} {{ props.config[ident] }}
        </div>
        <div v-else-if="props.config[ident].length > 0" class="panel bg-white m-2">
            {{ field.label }}
            <span class="letter-tiles">
                <span class="cyan small">
                    {{ props.config[ident].length }}
                </span>
            </span>
        </div>
    </div>
</template>

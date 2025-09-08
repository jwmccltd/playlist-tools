<script setup>
import { ref, computed } from 'vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const selectedDays = ref([]);
const frequency = ref('daily');
const live = ref(false);
const time = ref();
const errors = ref([]);

const props = defineProps({
    playlistLinkId: {
        type: String,
        required: true,
    },
    playlistConfigurationSchedule: {
        type: Object,
        default: () => {
            return {};
        },
    },
});

if (props.playlistConfigurationSchedule.length > 0)
{
    selectedDays.value = props.playlistConfigurationSchedule[0]['days'];
    frequency.value = props.playlistConfigurationSchedule[0]['frequency'];
    time.value = props.playlistConfigurationSchedule[0]['run_at_time'];
    live.value = props.playlistConfigurationSchedule[0]['activated'] !== null;
}

// Days of week data
const daysOfWeek = [
    { id: 'monday', label: 'Monday', short: 'Mon' },
    { id: 'tuesday', label: 'Tuesday', short: 'Tue' },
    { id: 'wednesday', label: 'Wednesday', short: 'Wed' },
    { id: 'thursday', label: 'Thursday', short: 'Thu' },
    { id: 'friday', label: 'Friday', short: 'Fri' },
    { id: 'saturday', label: 'Saturday', short: 'Sat' },
    { id: 'sunday', label: 'Sunday', short: 'Sun' },
];

// Computed properties
const isAllSelected = computed(() => selectedDays.value.length === daysOfWeek.length);
const isIndeterminate = computed(() =>
    selectedDays.value.length > 0 && selectedDays.value.length < daysOfWeek.length);

const toggleDay = (dayId) => {
    if (live.value === true) {
        return true;
    }

    if (selectedDays.value.includes(dayId)) {
        selectedDays.value = selectedDays.value.filter(id => id !== dayId);
    } else {
        selectedDays.value.push(dayId);
    }
};

const toggleAllDays = () => {
    if (live.value === true) {
        return true;
    }

    if (isAllSelected.value) {
        selectedDays.value = [];
    } else {
        selectedDays.value = daysOfWeek.map(day => day.id);
    }
};

const toggleConfigRunning = (event) => {
    if (event === true) {
        live.value = true;

        axios.post(
            route('spotify-playlist.set-schedule', { playlistLinkId: props.playlistLinkId }),
            {
                frequency: frequency.value,
                time: time.value,
                days: selectedDays.value,
            },
        ).then(function () {
            live.value = true;
            errors.value = [];
        }).catch(function (request) {
            setTimeout(() => {
                live.value = false;
            }, 500);

            errors.value = request.response.data.errors;
        });
    } else {
        axios.post(route('spotify-playlist.deactivate-schedule', { playlistLinkId: props.playlistLinkId }));
        live.value = false;
    }
};

</script>

<template>
    <div>
        <div>
            <div class="flex flex-row items-center">
                <div class="letter-tiles">
                    <span v-for="(letter, index) of ['S','C','H','E','D','U','L','E']" :key="index" class="small">
                        {{ letter }}
                    </span>
                </div>
                <ToggleSwitch
                    @check-box-on="toggleConfigRunning($event)"
                    :control-switch="true"
                    :checked="live === true"
                    :active-colour-class="'active-config'"
                    title="schedule on / off"
                    class="ml-auto"/>
            </div>
        </div>

        <div class="mx-auto p-6 bg-white rounded-lg shadow-lg">
            <!-- Frequency Selector -->
            <div class="mb-6">
                <label class="text-lg font-bold mb-3 block">
                    Frequency
                </label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input
                            v-model="frequency"
                            type="radio"
                            name="frequency"
                            value="hourly"
                            class="mr-2 h-4 w-4 text-blue-600"
                            :disabled="live">
                        <span class="text-sm ">Hourly</span>
                    </label>
                    <label class="flex items-center">
                        <input
                            v-model="frequency"
                            type="radio"
                            name="frequency"
                            value="daily"
                            class="mr-2 h-4 w-4 text-blue-600"
                            :disabled="live">
                        <span class="text-sm ">Daily</span>
                    </label>
                </div>
            </div>
            <div v-if="frequency === 'daily'" class="mb-6 w-1/2">
                <label class="text-lg font-bold mb-3 block">
                    Run At
                </label>
                <VueDatePicker v-model="time" time-picker :disabled="live"/>
                <div v-if="errors?.time" class="mt-2 text-red-600">
                    <p class="text-left">{{ errors['time'][0] }}</p>
                </div>
            </div>

            <!-- Days of Week Multi-Select -->
            <div>
                <label class="text-lg font-bold mb-3 block">
                    Days of Week
                </label>

                <!-- All Days Option -->
                <div
                    @click="toggleAllDays"
                    :class="[
                        'flex items-center justify-between p-3 mb-2 rounded-lg border-2 cursor-pointer transition-all',
                        isAllSelected
                            ? 'border-blue-500 bg-blue-50'
                            : isIndeterminate
                                ? 'border-blue-300 bg-blue-25'
                                : 'border-gray-200 hover:border-gray-300'
                    ]">
                    <span class="text-sm">All Days</span>
                    <div
                        :class="[
                            'w-5 h-5 rounded border-2 flex items-center justify-center',
                            isAllSelected
                                ? 'border-blue-500 bg-blue-500'
                                : isIndeterminate
                                    ? 'border-blue-500 bg-blue-500'
                                    : 'border-gray-300'
                        ]">
                        <svg v-if="isAllSelected" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <div v-else-if="isIndeterminate" class="w-2 h-2 bg-white rounded-sm"/>
                    </div>
                </div>

                <!-- Individual Days -->
                <div class="grid grid-cols-2 gap-2">
                    <div
                        v-for="day in daysOfWeek"
                        @click="toggleDay(day.id)"
                        :key="day.id"
                        :class="[
                            'flex items-center justify-between p-3 rounded-lg border-2 cursor-pointer transition-all',
                            selectedDays.includes(day.id)
                                ? 'border-blue-500 bg-blue-50'
                                : 'border-gray-200 hover:border-gray-300'
                        ]">
                        <span class="text-sm ">{{ day.short }}</span>
                        <div
                            :class="[
                                'w-4 h-4 rounded border-2 flex items-center justify-center',
                                selectedDays.includes(day.id)
                                    ? 'border-blue-500 bg-blue-500'
                                    : 'border-gray-300'
                            ]">
                            <svg v-if="selectedDays.includes(day.id)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div v-if="errors?.days" class="mt-2 text-red-600">
                    <p class="text-left">{{ errors['days'][0] }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    accountTypes: Object,
    member: Object
})
const acc_types = [
    // { id: 'account_type_2', src: '/assets/account_type/ecn.png', value: 2 },
    { id: 'account_type_1', src: '/assets/account_type/standard.png', value: 1 },
    // { id: 'account_type_4', src: '/assets/account_type/standard_islam.png', value: 4 },
];
const loading = ref(false);
const emit = defineEmits(['update:memberDetailModal']);

const form = useForm({
    id: props.member.id,
    account_type: '',
    ibGroupRates: {}
})

const submit = () => {
    form.post(route('member.upgradeIb'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        }
    })
}

const closeModal = () => {
    emit('update:memberDetailModal', false);
}

const symbolGroupRates = ref([]);

// Define a method to send the POST request when the input radio is clicked
const handleInputRadioClick = async () => {
    try {
        loading.value = true; // Show loading state

        const response = await axios.post('/member/getIBAccountTypeSymbolGroupRate', {
            id: props.member.id,
            account_type: props.accountTypes.id,
        });

        symbolGroupRates.value = response.data;
        loading.value = false; // Hide loading state after getting the response
    } catch (error) {
        console.error('Error fetching group data:', error);
        loading.value = false; // Hide loading state in case of an error
    }
};

const isRadioSelected = computed(() => {
    return form.account_type !== '';
});
</script>

<template>
    <div>
        <h2
            class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
        >
            Manage IB Account Type
        </h2>
        <div class="mt-6 space-y-2">
            <Label for="group" value="Select Account Type" />
            <ul class="grid w-full gap-6 md:grid-cols-3">
                <li v-for="(type, index) in acc_types" :key="index">
                    <input
                        type="radio"
                        :id="type.id"
                        name="group"
                        :value="props.accountTypes.id"
                        class="hidden peer"
                        v-model="form.account_type"
                        :required="type.required"
                        @click="handleInputRadioClick"
                    >
                    <label :for="type.id" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">
                        <div class="block">
                            <img class="object-cover" :src="type.src" alt="account_type">
                        </div>
                    </label>
                </li>
            </ul>
            <InputError :message="form.errors.account_type"/>
        </div>
        <div v-if="loading" class="w-full flex justify-center mt-4">
            <div class="px-4 py-2 text-sm font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                loading...
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" v-if="symbolGroupRates.length > 0">
            <div class="space-y-2 my-2" v-for="groupRate in symbolGroupRates">
                <div class="inline-flex gap-2">
                    <Label for="group" :value="groupRate.symbol_group.display" />
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">MAX: {{ groupRate.amount }}</span>
                </div>

                <Input
                    :id="`group_${groupRate.id}`"
                    class="w-full block"
                    type="number"
                    step="0.01" min="0.00"
                    :max="groupRate.amount"
                    v-model="form.ibGroupRates[groupRate.symbol_group.id]"
                />
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <Button variant="secondary" @click="closeModal">
                Cancel
            </Button>
            <Button v-if="isRadioSelected" @click.prevent="submit">
                Save
            </Button>
        </div>
    </div>
</template>

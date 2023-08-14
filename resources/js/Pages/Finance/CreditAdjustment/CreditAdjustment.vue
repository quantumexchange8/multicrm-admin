<script setup>
import {transactionFormat} from "@/Composables/index.js";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import {ref} from "vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";

const props = defineProps({
    account: Object,
    status: String
})

const { formatAmount } = transactionFormat();
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const form = useForm({
    user_id: props.account.user_id,
    account_no: props.account.meta_login,
    start_date: '',
    end_date: '',
    amount: '',
    internal_description: '',
    client_description: '',
    allotted_time: 0,
    type: 'credit_in',
});

const submitForm = () => {
    form.post(route('finance.credit_adjustment'), {
        onSuccess: () => {
            closeModal();
        },
    });
}
const emit = defineEmits(['update:creditAdjustmentModal']);
const closeModal = () => {
    emit('update:creditAdjustmentModal', false);
}

const handleAllottedTimeChange = (value) => {
    form.allotted_time = value;
};

const handleTypeChange = (value) => {
    form.reset();
    form.type = value;
};
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Credit Adjustment</h2>
    <hr>
    <ValidationErrors class="my-4" v-if="!form.errors.amount && !form.errors.start_date && !form.errors.end_date && !form.errors.internal_description && !form.errors.client_description"/>
    <ul class="grid w-full gap-6 md:grid-cols-2 mt-8">
        <li>
            <input type="radio" id="credit_in" v-model="form.type" value="credit_in" @change="handleTypeChange('credit_in')" class="hidden peer">
            <label for="credit_in" class="inline-flex items-center justify-between text-center w-full py-2 text-white bg-white border border-blue-600 rounded-full cursor-pointer dark:hover:text-white dark:border-blue-600 dark:peer-checked:text-white dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-blue-100 dark:text-white dark:bg-dark-eval-0 dark:hover:bg-blue-500">
                <span class="w-full">Add Credit</span>
            </label>
        </li>
        <li>
            <input type="radio" id="credit_out" v-model="form.type" value="credit_out" @change="handleTypeChange('credit_out')" class="hidden peer">
            <label for="credit_out" class="inline-flex items-center justify-between text-center w-full py-2 text-white bg-white border border-blue-600 rounded-full cursor-pointer dark:hover:text-white dark:border-blue-600 dark:peer-checked:text-white dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-blue-100 dark:text-white dark:bg-dark-eval-0 dark:hover:bg-blue-500">
                <span class="w-full">Withdraw Credit</span>
            </label>
        </li>
    </ul>

    <div class="flex justify-center flex-col text-center mt-8 space-y-2">
        <h4 class="text-lg font-medium text-gray-900 dark:text-dark-eval-4">Account Balance</h4>
        <h3 class="text-4xl mb-2 font-medium text-gray-900 dark:text-gray-100">
            $ {{ formatAmount(account.balance) }}
        </h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 mt-4 gap-6">
        <div class="space-y-6 mb-4 md:mb-0">
            <Label for="source">Credit Source</Label>
            <Input
                disabled
                class="w-full border-0"
                model-value="Broker"
            />
        </div>
        <div class="space-y-6 mb-4 md:mb-0">
            <Label for="credit">Available Credit Balance ($)</Label>
            <Input
                disabled
                class="w-full border-0"
                :model-value="account.credit"
            />
        </div>
        <div class="space-y-6 md:col-span-2 mb-4 md:mb-0" v-if="form.type === 'credit_in'">
            <Label for="amount">Allotted Time</Label>
            <div class="flex gap-12 mb-4">
                <div>
                    <input id="with_allotted_time" type="radio" v-model="form.allotted_time" value="1" @change="handleAllottedTimeChange(1)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="with_allotted_time" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">With Allotted Time</label>
                </div>
                <div>
                    <input id="without_allotted_time" type="radio" v-model="form.allotted_time" value="0" @change="handleAllottedTimeChange(0)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="without_allotted_time" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Without Allotted Time</label>
                </div>
            </div>
        </div>
        <div class="space-y-6 mb-4 md:mb-0" v-if="form.allotted_time === 1">
            <Label for="start_date">Start Date</Label>
            <vue-tailwind-datepicker
                id="start_date"
                :formatter="formatter"
                v-model="form.start_date"
                as-single
                input-classes="py-2 border-0 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
            />

            <InputError :message="form.errors.start_date" class="mt-2" />
        </div>
        <div class="space-y-6 mb-4 md:mb-0" v-if="form.allotted_time === 1">
            <Label for="end_date">End Date</Label>
            <vue-tailwind-datepicker
                id="end_date"
                :formatter="formatter"
                v-model="form.end_date"
                as-single
                input-classes="py-2 border-0 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
            />

            <InputError :message="form.errors.end_date" class="mt-2" />
        </div>

        <div class="space-y-6 md:col-span-2 mb-4 md:mb-0">
            <Label for="amount">Amount ($)</Label>
            <Input
                id="amount"
                placeholder="0.00"
                type="number"
                step="0.01"
                class="block w-full dark:border-0 text-xs"
                v-model="form.amount"
            />
            <InputError :message="form.errors.amount" class="mt-2" />
        </div>
        <div class="space-y-6 mb-4 md:mb-0">
            <Label for="internal_description">Internal Description</Label>
            <Input
                id="internal_description"
                class="block w-full dark:border-0 text-xs"
                v-model="form.internal_description"
            />
            <InputError :message="form.errors.internal_description" class="mt-2" />
        </div>
        <div class="space-y-6 mb-4 md:mb-0">
            <Label for="client_description">Description (Visible to Client)</Label>
            <Input
                id="client_description"
                class="block w-full dark:border-0 text-xs"
                v-model="form.client_description"
            />
            <InputError :message="form.errors.client_description" class="mt-2" />
        </div>
    </div>
    <div class="my-12 grid grid-cols-2 gap-4 float-right">
        <Button variant="secondary" class="px-6 justify-center" @click="closeModal">
            Cancel
        </Button>
        <Button class="px-6 justify-center" @click.prevent="submitForm" :disabled="form.processing">Confirm</Button>
    </div>
</template>

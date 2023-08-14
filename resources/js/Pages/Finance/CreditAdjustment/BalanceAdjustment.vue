<script setup>
import {transactionFormat} from "@/Composables/index.js";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import {ref} from "vue";

const props = defineProps({
    account: Object,
    status: String
})

const { formatAmount } = transactionFormat();

const form = useForm({
    user_id: props.account.user_id,
    account_no: props.account.meta_login,
    amount: '',
    comment: '',
    type: 'deposit',
});

const submitForm = () => {
    form.post(route('finance.balance_adjustment'), {
        onSuccess: () => {
            closeModal();
        },
    });
}
const emit = defineEmits(['update:creditAdjustmentModal']);
const closeModal = () => {
    emit('update:creditAdjustmentModal', false);
}
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Balance Adjustment</h2>
    <hr>
    <ValidationErrors class="my-4" v-if="!form.errors.amount && !form.errors.comment"/>
    <ul class="grid w-full gap-6 md:grid-cols-2 mt-8">
        <li>
            <input type="radio" id="deposit" v-model="form.type" value="deposit" class="hidden peer">
            <label for="deposit" class="inline-flex items-center justify-between text-center w-full py-2 text-white bg-white border border-blue-600 rounded-full cursor-pointer dark:hover:text-white dark:border-blue-600 dark:peer-checked:text-white dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-blue-100 dark:text-white dark:bg-dark-eval-0 dark:hover:bg-blue-500">
                <span class="w-full">Deposit Balance</span>
            </label>
        </li>
        <li>
            <input type="radio" id="withdrawal" v-model="form.type" value="withdrawal" class="hidden peer">
            <label for="withdrawal" class="inline-flex items-center justify-between text-center w-full py-2 text-white bg-white border border-blue-600 rounded-full cursor-pointer dark:hover:text-white dark:border-blue-600 dark:peer-checked:text-white dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-blue-100 dark:text-white dark:bg-dark-eval-0 dark:hover:bg-blue-500">
                <span class="w-full">Withdraw Balance</span>
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
        <div class="space-y-2 mb-4 md:mb-0">
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
        <div class="space-y-2 mb-4 md:mb-0">
            <Label for="description">Description</Label>
            <Input
                id="description"
                class="block w-full dark:border-0 text-xs"
                v-model="form.comment"
            />
            <InputError :message="form.errors.comment" class="mt-2" />
        </div>
    </div>
    <div class="my-6 grid grid-cols-2 gap-4 float-right">
        <Button variant="secondary" class="px-6 justify-center" @click="closeModal">
            Cancel
        </Button>
        <Button class="px-6 justify-center" @click.prevent="submitForm" :disabled="form.processing">Confirm</Button>
    </div>
</template>

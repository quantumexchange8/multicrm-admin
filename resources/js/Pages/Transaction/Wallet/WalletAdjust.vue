<script setup>
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    user: Object
})
const { formatAmount } = transactionFormat();
const emit = defineEmits(['update:userWalletDetailModal']);
const closeModal = () => {
    emit('update:userWalletDetailModal', false);
}

const form = useForm({
    id: props.user.id,
    amount: '',
    comment: '',
});

const submitForm = () => {
    form.post(route('transaction.wallet_adjustment'), {
        onSuccess: () => {
            closeModal();
        },
    });
}
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Fund Adjustment</h2>
    <hr>
    <div class="flex justify-center flex-col text-center mt-8 space-y-2">
        <h4 class="text-lg font-medium text-gray-900 dark:text-dark-eval-4">Cash Wallet Balance</h4>
        <h3 class="text-4xl mb-2 font-medium text-gray-900 dark:text-gray-100">
            $ {{ formatAmount(user.cash_wallet) }}
        </h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 mt-4 gap-6">
        <div class="space-y-2 mb-4 md:mb-0">
            <Label for="amount">Adjust Amount ($)</Label>
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

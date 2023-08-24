<script setup>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import {router, useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

const props = defineProps({
    account: Object,
    isEditing: Boolean,
})

const emit = defineEmits(['update:isEditing', 'update:paymentAccountModal']);

const crypto_types = [
    { src: '/assets/finance/crypto/bitcoin.png', value: 'Bitcoin (BTC)', name: 'Bitcoin (BTC)' },
    { src: '/assets/finance/crypto/ethereum.png', value: 'Ethereum (ETH)', name: 'Ethereum (ETH)' },
    { src: '/assets/finance/crypto/usdt-tron.png', value: 'USDT (TRON)', name: 'USDT (TRON)' },
    { src: '/assets/finance/crypto/usdt-eth.png', value: 'USDT (ETH)', name: 'USDT (ETH)' },
];

const form = useForm({
    payment_account_id: props.account.id,
    payment_platform_name: props.account.payment_platform_name,
    payment_platform: props.account.payment_platform,
    payment_account_name: props.account.payment_account_name,
    account_no: props.account.account_no,
})

const submit = () => {
    if (props.isEditing) {
        form.patch(route('finance.update_payment_account'), {
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        emit('update:isEditing', true);
    }
};

const closeModal = () => {
    emit('update:paymentAccountModal', false);
    emit('update:isEditing', false);
}

const toggleEdit = () => {
    if (props.isEditing) {
        closeModal();
    } else {
        emit('update:isEditing', true);
    }
}

</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Edit Cryptocurrency Wallet Details</h2>
    <hr>
    <form>
        <div class="my-6">
            <Label for="type" value="Select a USDT protocol type " />
            <ul class="my-4 grid w-full gap-6 grid-cols-2 md:grid-cols-4">
                <li v-for="(crypto, index) in crypto_types" :key="index">
                    <input
                        type="radio"
                        :id="`crypto_type_${index}`"
                        name="payment_platform_name"
                        :value="crypto.value"
                        class="hidden peer"
                        v-model="form.payment_platform_name"
                        :disabled="!isEditing"
                    >
                    <label
                        :for="`crypto_type_${index}`"
                        class="inline-flex items-center justify-center w-full py-2 px-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <img class="object-cover h-8" :src="crypto.src" alt="account_platform">
                            <p class="dark:text-white text-xs">{{ crypto.name }}</p>
                        </div>
                    </label>
                </li>
            </ul>
            <InputError :message="form.errors.payment_platform_name"/>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <Label for="payment_account_name" value="USDT e-Wallet Name" />

                <Input
                    id="payment_account_name"
                    type="text"
                    class="block w-full px-4"
                    placeholder="example - BINANCE WALLET"
                    v-model="form.payment_account_name"
                    autocomplete="off"
                    :disabled="!isEditing"
                />
                <InputError :message="form.errors.payment_account_name"/>

            </div>
            <div class="space-y-2">
                <Label for="account_no" value="Token Address" />

                <Input
                    id="account_no"
                    type="text"
                    class="block w-full px-4"
                    v-model="form.account_no"
                    autocomplete="off"
                    :disabled="!isEditing"
                />
                <InputError :message="form.errors.account_no"/>

            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <Button variant="secondary" v-if="isEditing" @click.prevent="toggleEdit">
                Cancel
            </Button>
            <Button
                variant="primary"
                class="ml-3"
                @click.prevent="submit"
                :disabled="form.processing"
            >
                {{ props.isEditing ? 'Save' : 'Edit' }}
            </Button>
        </div>
    </form>
</template>

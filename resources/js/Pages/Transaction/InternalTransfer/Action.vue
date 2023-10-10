<script setup>
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {ViewIcon} from "@/Components/Icons/outline.jsx";
import Modal from "@/Components/Modal.vue";
import {transactionFormat} from "@/Composables/index.js";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    history: Object
})
const { formatAmount, formatType, formatDate } = transactionFormat();
const internalTransferModal = ref(false);

const openWalletModal = () => {
    internalTransferModal.value = true;
}

const closeModal = () => {
    internalTransferModal.value = false
}
</script>

<template>
    <Tooltip :content="$t('public.View')" placement="top">
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openWalletModal"
        >
            <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
        </Button>
    </Tooltip>

    <Modal :show="internalTransferModal" @close="closeModal" max-width="7xl">
        <div class="p-6">
            <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.View More Details') }}</h2>
            <hr>

            <div class="flex justify-center flex-col text-center mt-8 space-y-2">
                <h4 class="text-lg font-medium text-gray-900 dark:text-dark-eval-4">{{ $t('public.Cash Wallet Balance') }}</h4>
                <h3 class="text-4xl mb-2 font-medium text-gray-900 dark:text-gray-100">
                    $ {{ formatAmount(history.of_user.cash_wallet) }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 mt-4 gap-6">
                <div class="space-y-2 mb-4 md:mb-0">
                    <Label for="type">{{ $t('public.Internal Transfer Type') }}</Label>
                    <Input
                        id="type"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        :model-value="formatType(history.type)"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0">
                    <Label for="amount">{{ $t('public.Amount') }}</Label>
                    <Input
                        id="amount"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        :model-value="'$ ' + formatType(history.amount)"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0" v-if="history.type==='AccountToWallet' || history.type==='AccountToAccount'">
                    <Label for="account_platform_transfer">{{ $t('public.Account Platform To Transfer') }}</Label>
                    <Input
                        id="account_platform_transfer"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        model-value="CTrader"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0" v-if="history.type==='AccountToWallet' || history.type==='AccountToAccount'">
                    <Label for="account_no_transfer">{{ $t('public.Account No. To Transfer') }}</Label>
                    <Input
                        id="account_no_transfer"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        :model-value="history.from"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0" v-if="history.type==='WalletToAccount' || history.type==='AccountToAccount'">
                    <Label for="account_platform_receive">{{ $t('public.Account Platform To Receive') }}</Label>
                    <Input
                        id="account_platform_receive"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        model-value="CTrader"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0" v-if="history.type==='WalletToAccount' || history.type==='AccountToAccount'">
                    <Label for="account_no_receive">{{ $t('public.Account No. To Receive') }}</Label>
                    <Input
                        id="account_no_receive"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        :model-value="history.to"
                    />
                </div>
                <div class="space-y-2 mb-4 md:mb-0" v-if="history.type==='RebateToWallet'">
                    <Label for="account_platform_receive">{{ $t('public.Date') }}</Label>
                    <Input
                        id="account_platform_receive"
                        class="block w-full dark:border-0 text-xs"
                        disabled
                        :model-value="formatDate(history.created_at)"
                    />
                </div>
            </div>
        </div>
    </Modal>
</template>

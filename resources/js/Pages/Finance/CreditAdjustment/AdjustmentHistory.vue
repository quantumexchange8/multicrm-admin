<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import BalanceHistory from "@/Pages/Finance/CreditAdjustment/BalanceHistory.vue";
import CreditHistory from "@/Pages/Finance/CreditAdjustment/CreditHistory.vue";

const props = defineProps({
    account: Object
})

const activeComponent = ref("balance"); // 'pending' is initially active

const setActiveComponent = (component) => {
    activeComponent.value = component;
};
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.View Adjustment History') }}</h2>
    <hr>

    <div class="grid grid-cols-2 my-8 gap-6">
        <Button
            variant="primary-opacity"
            class="px-6 border border-blue-600 justify-center text-white mt-4 focus:ring-0"
            :class="{ 'bg-transparent': activeComponent !== 'cash_wallet', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'balance' }"
            @click="setActiveComponent('balance')"
        >
            {{ $t('public.Balance History') }}
        </Button>
        <Button
            variant="primary-opacity"
            class="px-6 border border-blue-600 justify-center text-white mt-4 focus:ring-0"
            :class="{ 'bg-transparent': activeComponent !== 'rebate_wallet', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'credit' }"
            @click="setActiveComponent('credit')"
        >
            {{ $t('public.Credit History') }}
        </Button>
    </div>

    <BalanceHistory
        v-if="activeComponent === 'balance'"
        :account="account"
    />
    <CreditHistory
        v-if="activeComponent === 'credit'"
        :account="account"
    />
</template>

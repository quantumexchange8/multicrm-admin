<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import CashWalletHistory from "@/Pages/Transaction/Wallet/CashWalletHistory.vue";
import RebateWalletHistory from "@/Pages/Transaction/Wallet/RebateWalletHistory.vue";

const props = defineProps({
    user: Object,
})

const activeComponent = ref("cash_wallet"); // 'pending' is initially active

const setActiveComponent = (component) => {
    activeComponent.value = component;
};
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View Transaction History</h2>
    <hr>

    <div class="grid grid-cols-2 my-8 gap-6">
        <Button
            variant="primary-opacity"
            class="px-6 border border-blue-600 justify-center mt-4 focus:ring-0"
            :class="{ 'bg-transparent': activeComponent !== 'cash_wallet', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'cash_wallet' }"
            @click="setActiveComponent('cash_wallet')"
        >
            Cash Wallet Transaction
        </Button>
        <Button
            v-if="user.role ==='ib'"
            variant="primary-opacity"
            class="px-6 border border-blue-600 justify-center mt-4 focus:ring-0"
            :class="{ 'bg-transparent': activeComponent !== 'rebate_wallet', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'rebate_wallet' }"
            @click="setActiveComponent('rebate_wallet')"
        >
            Rebate Wallet Transaction
        </Button>
    </div>

    <CashWalletHistory
        v-if="activeComponent === 'cash_wallet'"
        :user="user"
    />
    <RebateWalletHistory
        v-if="activeComponent === 'rebate_wallet'"
        :user="user"
    />
</template>

<script setup>
import {ref} from "vue";
import {GearIcon, ViewIcon, GiftIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import CreditAdjustment from "@/Pages/Finance/CreditAdjustment/CreditAdjustment.vue";
import AdjustmentHistory from "@/Pages/Finance/CreditAdjustment/AdjustmentHistory.vue";
import BalanceAdjustment from "@/Pages/Finance/CreditAdjustment/BalanceAdjustment.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    account: Object,
})

const creditAdjustmentModal = ref(false);
const modalComponent = ref(null);

const openWalletModal = (ibId, componentType) => {
    creditAdjustmentModal.value = true;
    if (componentType === 'balanceAdjust') {
        modalComponent.value = 'BalanceAdjust';
    } else if (componentType === 'creditAdjust') {
        modalComponent.value = 'CreditAdjustment';
    } else if (componentType === 'view') {
        modalComponent.value = 'ViewHistory';
    }
}

const closeModal = () => {
    creditAdjustmentModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <div class="flex justify-center">
        <Tooltip content="Balance Adjustment" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openWalletModal(account.id, 'balanceAdjust')"
            >
                <GearIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip content="Credit Adjustment" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openWalletModal(account.id, 'creditAdjust')"
            >
                <GiftIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip content="View" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openWalletModal(account.id, 'view')"
            >
                <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>

    </div>

    <Modal :show="creditAdjustmentModal" @close="closeModal" max-width="7xl">
        <div class="p-6">

            <!-- Balance Adjust -->
            <template v-if="modalComponent === 'BalanceAdjust'">
                <BalanceAdjustment
                    :account="account"
                    @update:creditAdjustmentModal="creditAdjustmentModal = $event"
                />
            </template>

            <!-- Credit Adjust -->
            <template v-if="modalComponent === 'CreditAdjustment'">
                <CreditAdjustment
                    :account="account"
                    @update:creditAdjustmentModal="creditAdjustmentModal = $event"
                />
            </template>

            <!-- View -->
            <template v-if="modalComponent === 'ViewHistory'">
                <AdjustmentHistory
                    :account="account"
                    @update:creditAdjustmentModal="creditAdjustmentModal = $event"
                />
            </template>
        </div>
    </Modal>
</template>

<script setup>
import {ref} from "vue";
import {GearIcon, ViewIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import CreditAdjustment from "@/Pages/Finance/CreditAdjustment/CreditAdjustment.vue";
import CreditHistory from "@/Pages/Finance/CreditAdjustment/CreditHistory.vue";

const props = defineProps({
    account: Object,
})

const creditAdjustmentModal = ref(false);
const modalComponent = ref(null);

const openWalletModal = (ibId, componentType) => {
    creditAdjustmentModal.value = true;
    if (componentType === 'adjust') {
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
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openWalletModal(account.id, 'adjust')"
        >
            <GearIcon aria-hidden="true" class="w-6 h-6 absolute" />
        </Button>
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openWalletModal(account.id, 'view')"
        >
            <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
        </Button>
    </div>

    <Modal :show="creditAdjustmentModal" @close="closeModal" max-width="7xl">
        <div class="p-6">

            <!-- Adjust -->
            <template v-if="modalComponent === 'CreditAdjustment'">
                <CreditAdjustment
                    :account="account"
                    @update:creditAdjustmentModal="creditAdjustmentModal = $event"
                />
            </template>

            <!-- View -->
            <template v-if="modalComponent === 'ViewHistory'">
                <CreditHistory
                    :account="account"
                    @update:creditAdjustmentModal="creditAdjustmentModal = $event"
                />
            </template>
        </div>
    </Modal>
</template>
<script setup>
import Button from "@/Components/Button.vue";
import {ViewIcon, GearIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import WalletAdjust from "@/Pages/Transaction/Wallet/WalletAdjust.vue";
import TransactionHistory from "@/Pages/Transaction/Wallet/TransactionHistory.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    user: Object
})

const userWalletDetailModal = ref(false);
const modalComponent = ref(null);

const openWalletModal = (ibId, componentType) => {
    userWalletDetailModal.value = true;
    if (componentType === 'adjust') {
        modalComponent.value = 'WalletAdjust';
    } else if (componentType === 'view') {
        modalComponent.value = 'ViewHistory';
    }
}

const closeModal = () => {
    userWalletDetailModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <div class="flex justify-center">
        <Tooltip :content="$t('public.Fund Adjustment')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openWalletModal(user.id, 'adjust')"
            >
                <GearIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.View')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openWalletModal(user.id, 'view')"
            >
                <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
    </div>

    <Modal :show="userWalletDetailModal" @close="closeModal" max-width="7xl">
        <div class="p-6">

            <!-- Adjust -->
            <template v-if="modalComponent === 'WalletAdjust'">
                <WalletAdjust
                    :user="user"
                    @update:userWalletDetailModal="userWalletDetailModal = $event"
                />
            </template>

            <!-- View -->
            <template v-if="modalComponent === 'ViewHistory'">
                <TransactionHistory
                    :user="user"
                    @update:userWalletDetailModal="userWalletDetailModal = $event"
                />
            </template>

        </div>
    </Modal>
</template>

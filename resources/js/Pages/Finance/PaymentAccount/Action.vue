<script setup>
import {ref} from "vue";
import {EditIcon, ViewIcon, TrashIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import BankAccountForm from "@/Pages/Finance/PaymentAccount/BankAccountForm.vue";
import CryptoWalletForm from "@/Pages/Finance/PaymentAccount/CryptoWalletForm.vue";
import DeleteAccountForm from "@/Pages/Finance/PaymentAccount/DeleteAccountForm.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    account: Object,
    countries: Object,
    bankProof: Object,
})

const paymentAccountModal = ref(false);
const modalComponent = ref(null);
const isEditing = ref(false);
const openAccountModal = (ibId, componentType) => {
    paymentAccountModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewAccount';
    } else if (componentType === 'edit') {
        modalComponent.value = 'EditAccount';
    } else if (componentType === 'delete') {
        modalComponent.value = 'DeleteAccount';
    }
}

const closeModal = () => {
    paymentAccountModal.value = false
    modalComponent.value = null;
    isEditing.value = false
}
</script>

<template>
    <div class="flex justify-center">
        <Tooltip content="View" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openAccountModal(account.id, 'view')"
            >
                <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip content="Edit" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openAccountModal(account.id, 'edit')"
            >
                <EditIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip content="Delete" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="danger-opacity"
                @click="openAccountModal(account.id, 'delete')"
            >
                <TrashIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>

    </div>

    <Modal :show="paymentAccountModal" @close="closeModal" :max-width="modalComponent === 'DeleteAccount' ? '2xl' : '7xl'">
        <div class="p-6">

            <!-- View Bank Account -->
            <template v-if="modalComponent === 'ViewAccount' && account.payment_platform ==='bank'">
                <BankAccountForm
                    :account="account"
                    :countries="countries"
                    :isEditing="isEditing"
                    :bankProof="bankProof"
                    @update:isEditing="isEditing = $event"
                />
            </template>

            <!-- Edit Bank Account -->
            <template v-if="modalComponent === 'EditAccount' && account.payment_platform ==='bank'">
                <BankAccountForm
                    :account="account"
                    :countries="countries"
                    :isEditing="isEditing = true"
                    :bankProof="bankProof"
                    @update:isEditing="isEditing = $event"
                    @update:paymentAccountModal="paymentAccountModal = $event"
                />
            </template>

            <!-- View Crypto Account -->
            <template v-if="modalComponent === 'ViewAccount' && account.payment_platform ==='crypto'">
                <CryptoWalletForm
                    :account="account"
                    :isEditing="isEditing"
                    @update:isEditing="isEditing = $event"
                />
            </template>

            <!-- Edit Crypto Account -->
            <template v-if="modalComponent === 'EditAccount' && account.payment_platform ==='crypto'">
                <CryptoWalletForm
                    :account="account"
                    :isEditing="isEditing = true"
                    @update:isEditing="isEditing = $event"
                    @update:paymentAccountModal="paymentAccountModal = $event"
                />
            </template>

            <!-- Delete Account -->
            <template v-if="modalComponent === 'DeleteAccount'">
                <DeleteAccountForm
                    :account="account"
                    @update:paymentAccountModal="paymentAccountModal = $event"
                />
            </template>

        </div>
    </Modal>
</template>

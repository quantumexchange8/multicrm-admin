<script setup>
import Button from "@/Components/Button.vue";
import {ViewIcon} from "@/Components/Icons/outline.jsx";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import Approval from "@/Pages/Transaction/Withdrawal/Approval.vue";
import View from "@/Pages/Transaction/Withdrawal/View.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    withdrawal: Object,
    type: String,
})

const withdrawalActionModal = ref(false);
const modalComponent = ref(null);
const modalTitle = ref('');

const openWithdrawalActionModal = (withdrawalId, componentType, title) => {
    withdrawalActionModal.value = true;
    if (componentType === 'approval') {
        modalComponent.value = 'WithdrawalApproval';
        modalTitle.value = title;
    } else if (componentType === 'view') {
        modalComponent.value = 'WithdrawalView';
    }
}

const closeModal = () => {
    withdrawalActionModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <div class="flex gap-2 justify-center">
        <Button
            v-if="type !== 'history'"
            variant="success-opacity"
            class="text-xs justify-center"
            @click="openWithdrawalActionModal(withdrawal.id, 'approval', 'Approve')"
        >
            Approve
        </Button>
        <Button
            v-if="type !== 'history'"
            variant="danger-opacity"
            class="text-xs justify-center"
            @click="openWithdrawalActionModal(withdrawal.id, 'approval', 'Reject')"
        >
            Reject
        </Button>
        <div class="flex justify-center items-center">
            <Tooltip content="View" placement="top">
                <Button
                    class="justify-center px-4 pt-2 rounded-full w-7 h-7 focus:outline-none"
                    variant="primary-opacity"
                    @click="openWithdrawalActionModal(withdrawal.id, 'view')"
                >
                    <ViewIcon aria-hidden="true" class="w-5 h-5 absolute" />
                    <span class="sr-only">View</span>
                </Button>
            </Tooltip>
        </div>

        <!-- Action Modal -->
        <Modal :show="withdrawalActionModal" @close="closeModal" max-width="4xl">
            <div class="p-6">

                <!-- Approval -->
                <template v-if="modalComponent === 'WithdrawalApproval'">
                    <Approval
                        :withdrawal="withdrawal"
                        :title="modalTitle"
                        @update:withdrawalActionModal="withdrawalActionModal = $event"
                    />
                </template>

                <!-- View -->
                <template v-if="modalComponent === 'WithdrawalView'">
                    <View
                        :withdrawal="withdrawal"
                        :type="type"
                        @update:withdrawalActionModal="withdrawalActionModal = $event"
                    />
                </template>
            </div>
        </Modal>

    </div>
</template>

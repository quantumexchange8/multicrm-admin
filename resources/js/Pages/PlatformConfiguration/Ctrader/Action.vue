<script setup>
import Button from "@/Components/Button.vue";
import {GearIcon, TrashIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import EditAccountType from "@/Pages/PlatformConfiguration/Ctrader/EditAccountType.vue";
import DeleteAccountType from "@/Pages/PlatformConfiguration/Ctrader/DeleteAccountType.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    account: Object,
    leverages: Object,
    groups: Object,
})

const accountTypeModal = ref(false);
const modalComponent = ref(null);

const openManageModal = (ibId, componentType) => {
    accountTypeModal.value = true;
    if (componentType === 'edit') {
        modalComponent.value = 'ManageAccount';
    } else if (componentType === 'delete') {
        modalComponent.value = 'DeleteAccount';
    }
}

const closeModal = () => {
    accountTypeModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <div class="flex justify-center">
        <Tooltip :content="$t('public.Setting')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openManageModal(account.id, 'edit')"
            >
                <GearIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.Delete')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="danger-opacity"
                @click="openManageModal(account.id, 'delete')"
            >
                <TrashIcon aria-hidden="true" class="w-6 h-6 absolute" />
            </Button>
        </Tooltip>

        <Modal :show="accountTypeModal" @close="closeModal" :max-width="modalComponent === 'DeleteAccount' ? '2xl' : '7xl'">
            <div class="p-6">
                <template v-if="modalComponent === 'ManageAccount'">
                    <EditAccountType
                        :account="account"
                        :leverages="leverages"
                        :groups="groups"
                        @update:accountTypeModal="accountTypeModal = $event"
                    />
                </template>

                <template v-if="modalComponent === 'DeleteAccount'">
                    <DeleteAccountType
                        :account="account"
                        @update:accountTypeModal="accountTypeModal = $event"
                    />
                </template>
            </div>
        </Modal>
    </div>
</template>

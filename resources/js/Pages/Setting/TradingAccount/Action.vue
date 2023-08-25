<script setup>
import {RefreshIcon, EditIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import Swal from "sweetalert2";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import ManageGroup from "@/Pages/Setting/TradingAccount/ManageGroup.vue";

async function refreshGroup() {

    try {
        const response = await axios.get('/setting/refreshGroup');
        if (response.data.success) {
            await Swal.fire({
                title: 'Success',
                text: response.data.message,
                icon: 'success',
                background: '#202020',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        } else {
            console.log(response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            await Swal.fire({
                title: 'Error',
                text: error.response.data.message,
                icon: 'error',
                background: '#202020',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'An error occurred while refreshing the groups.',
                icon: 'error',
                background: '#202020',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        }
    }
}

const accountSettingModal = ref(false);
const modalComponent = ref(null);
const openAccountSettingModal = (componentType) => {
    accountSettingModal.value = true;
    if (componentType === 'manage') {
        modalComponent.value = 'ManageAccount';
    }
}

const closeModal = () => {
    accountSettingModal.value = false
}

</script>

<template>
    <div class="flex gap-2 justify-center">
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="refreshGroup"
        >
            <RefreshIcon aria-hidden="true" class="w-6 h-6 absolute" />
        </Button>
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openAccountSettingModal('manage')"
        >
            <EditIcon aria-hidden="true" class="w-6 h-6 absolute" />
        </Button>
    </div>

    <Modal :show="accountSettingModal" @close="closeModal" max-width="4xl">
        <div class="p-6">

            <!-- Manage Groups -->
            <template v-if="modalComponent === 'ManageAccount'">
                <ManageGroup />
            </template>

        </div>
    </Modal>
</template>

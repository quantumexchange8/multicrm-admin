<script setup>
import Button from "@/Components/Button.vue";
import {GearIcon, IbTransferIcon, ViewIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import ViewRebate from "@/Pages/Member/RebateAllocation/ViewRebate.vue";
import StructureRebate from "@/Pages/Member/RebateAllocation/StructureRebate.vue";
import Swal from "sweetalert2";
import PayoutDetails from "@/Pages/Member/RebatePayout/PayoutDetails.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    list: Object,
    date: String,
    status: String
})

const IbManageModal = ref(false);
const modalComponent = ref(null);

const openRebateDetails = (ibId, componentType) => {
    IbManageModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewRebate';
    } else if (componentType === 'structure') {
        modalComponent.value = 'StructureRebate';
    } else if (componentType === 'transferIb') {
        modalComponent.value = 'TransferIB';
    }
}

const closeModal = () => {
    IbManageModal.value = false
    modalComponent.value = null;
}

async function confirmRebatePayout(amount) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500 mx-2',
            cancelButton: 'bg-transparent text-[#AF60FF] py-2 px-6 rounded-full text-white hover:bg-dark-eval-1 focus:ring-red-500 mx-2',
        },
        buttonsStyling: false,
        background: '#000000',
        iconColor: '#ffffff',
        color: '#ffffff',
    });

    const result = await swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: `A total of $${amount.toFixed(2)} will be paid to the selected IB!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        await rebate_payout();
    }
}

function showConfirmation(amount) {
    confirmRebatePayout(amount);
}

async function rebate_payout() {
    // Implement your rebate payout logic here
    try {
        // Make the POST request using axios
        const response = await axios.post('/member/approve_rebate_payout', {
            ib_account_types_id: props.list.ib_account_types_id,
            date: props.date,
            type: 'approve_single',
            close_date: props.list.date,
        });

        if (response.data.success) {
            await Swal.fire({
                title: 'Success',
                text: response.data.message,
                icon: 'success',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            }).then(() => {
                // Reload the page after the SweetAlert is closed
                location.reload();
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
                background: '#000000',
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
                text: 'An error occurred while applying the rebate.',
                icon: 'error',
                background: '#000000',
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

const loading = ref(false);
const rebatePayoutDetails = ref([]);

const handleViewButton = () => {
    getRebatePayoutInfo();
    openRebateDetails(props.list.id, 'view');
};

const getRebatePayoutInfo = async () => {
    try {
        loading.value = true; // Show loading state

        const response = await axios.post('/member/getRebatePayoutDetails', {
            id: props.list.ib_account_types_id,
            date: props.list.date,
            status: props.status
        });

        rebatePayoutDetails.value = response.data;
        loading.value = false; // Hide loading state after getting the response
    } catch (error) {
        console.error('Error fetching rebate payout detail: ', error);
        loading.value = false; // Hide loading state in case of an error
    }
};

</script>

<template>
    <div class="flex justify-center items-center">
        <Button
            v-if="status === 'pending'"
            variant="success-opacity"
            @click.prevent="showConfirmation(list.total_revenue)"
            class="text-xs"
        >
            Approve
        </Button>
        <Tooltip content="View" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-7 h-7 focus:outline-none"
                variant="primary-opacity"
                @click="handleViewButton"
            >
                <ViewIcon aria-hidden="true" class="w-5 h-5 absolute" />
                <span class="sr-only">View</span>
            </Button>
        </Tooltip>
    </div>

    <!-- Action Modal -->
    <Modal :show="IbManageModal" @close="closeModal" max-width="7xl">
        <div class="p-6">

            <!-- View -->
            <template v-if="modalComponent === 'ViewRebate'">
                <div v-if="loading" class="w-full flex justify-center mt-4">
                    <div class="px-4 py-2 text-sm font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                        loading...
                    </div>
                </div>
                <PayoutDetails
                    v-else
                    :list="list"
                    :rebatePayoutDetails="rebatePayoutDetails"
                />
            </template>

        </div>
    </Modal>
</template>


<script setup>
import Action from "@/Pages/Member/RebatePayout/Action.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {computed, ref, watch} from "vue";
import Button from "@/Components/Button.vue";
import Swal from "sweetalert2";
import Paginator from "@/Components/Paginator.vue";

const props = defineProps({
    lists: Object,
    date: String
})

const selectAllChecked = ref(false);
const selectedItems = ref([]);

// Watch for changes in selectedItems array and update selectAllChecked accordingly
watch(selectedItems, () => {
    selectAllChecked.value = selectedItems.value.length === props.lists.data.length;
});

function toggleAllCheckboxes() {
    if (selectAllChecked.value) {
        selectedItems.value = props.lists.data.map((list) => ({
            ib_account_types_id: list.ib_account_types_id,
            closed_date: list.date, // Add the closed_date from the list
        }));
    } else {
        selectedItems.value = [];
    }
}

function toggleItemCheckbox(itemValue, closedDate) {
    const existingIndex = selectedItems.value.findIndex((item) => item.ib_account_types_id === itemValue);

    if (existingIndex !== -1) {
        selectedItems.value.splice(existingIndex, 1);
    } else {
        selectedItems.value.push({ ib_account_types_id: itemValue, closed_date: closedDate });
    }
}

function isItemSelected(itemValue) {
    return selectedItems.value.some((item) => item.ib_account_types_id === itemValue);
}

const showConfirmButton = computed(() => {
    return selectAllChecked.value || selectedItems.value.length > 0;
});


async function confirmAction() {
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
        text: `Approve all selected IB!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        await approveSelectedRebatePayout();
    }
}

async function approveSelectedRebatePayout() {
    try {
        // Make the POST request using axios with selectedItems
        const response = await axios.post('/member/approve_rebate_payout', {
            selected_items: selectedItems.value,
            date: props.date,
            type: 'approve_selected',
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

</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
        <tr>
            <th scope="col" class="px-6 py-3">
                <Checkbox
                    v-model="selectAllChecked"
                    @change="toggleAllCheckboxes"
                />
            </th>
            <th scope="col" class="px-6 py-3">
                Date
            </th>
            <th scope="col" class="px-6 py-3">
                IB Name
            </th>
            <th scope="col" class="px-6 py-3">
                Account Number
            </th>
            <th scope="col" class="px-6 py-3">
                Account Type
            </th>
            <th scope="col" class="px-6 py-3">
                Total Volume (LOTS)
            </th>
            <th scope="col" class="px-6 py-3">
                Total Payout
            </th>
            <th scope="col" class="px-6 py-3">
                Action
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="lists.data.length === 0">
            <th colspan="8" class="py-4 text-lg text-center">
                No Pending
            </th>
        </tr>
        <tr v-for="list in lists.data" :key="list.ib_account_types_id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th class="py-2 font-thin rounded-l-full">
                <Checkbox
                    :checked="selectAllChecked || isItemSelected(list.ib_account_types_id)"
                    @change="toggleItemCheckbox(list.ib_account_types_id)"
                    :value="list.ib_account_types_id"
                />
            </th>
            <th>
                 {{ list.date }}
            </th>
            <th class="px-6 py-4">
                 {{ list.of_user.first_name }}
            </th>
            <th>
                 {{ list.meta_login }}
            </th>
            <th>
                 {{ (list.of_account_type.name ) }}
            </th>
            <th>
                 {{ list.total_volume.toFixed(2) }}
            </th>
            <th>
                 {{ list.total_revenue.toFixed(2) }}
            </th>
            <th class="px-6 py-2 font-thin rounded-r-full">
                <Action
                    :list="list"
                    :date="date"
                    status="pending"
                />
            </th>
        </tr>
        </tbody>
    </table>
    <div class="flex justify-end mt-4">
        <Paginator :links="props.lists.links" />
    </div>
    <div class="flex justify-end">
        <Button
            v-if="showConfirmButton"
            class="float-right text-xs"
            @click="confirmAction"
        >
            Confirm
        </Button>
    </div>
</template>

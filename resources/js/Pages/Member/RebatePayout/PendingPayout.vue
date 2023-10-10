<script setup>
import Action from "@/Pages/Member/RebatePayout/Action.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {computed, ref, watch} from "vue";
import Button from "@/Components/Button.vue";
import Swal from "sweetalert2";
import Paginator from "@/Components/Paginator.vue";
import {trans} from "laravel-vue-i18n";

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
            closed_date: list.date,
            meta_login: list.meta_login,
            total_volume: list.total_volume,
            total_revenue: list.total_revenue,
        }));
    } else {
        selectedItems.value = [];
    }
}

function toggleItemCheckbox(itemValue, closedDate, meteLogin, totalVolume, totalRevenue) {
    selectedItems.value.push({
        ib_account_types_id: itemValue,
        closed_date: closedDate,
        meta_login: meteLogin,
        total_volume: totalVolume,
        total_revenue: totalRevenue,
    });
}

function isItemSelected(itemValue, closedDate, metaLogin, totalVolume, totalRevenue) {
    return selectedItems.value.some(item =>
        item.ib_account_types_id === itemValue &&
        item.closed_date === closedDate &&
        item.meta_login === metaLogin &&
        item.total_volume === totalVolume &&
        item.total_revenue === totalRevenue
    );
}

const showConfirmButton = computed(() => {
    return selectAllChecked.value || selectedItems.value.length > 0;
});


async function confirmAction(type) {
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

    if (type === 'approve') {
        const result = await swalWithBootstrapButtons.fire({
            title: trans('public.Are you sure?'),
            text: trans('public.Approve all selected IB!'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: trans('public.Confirm'),
            cancelButtonText: trans('public.Cancel'),
            reverseButtons: true,
        });

        if (result.isConfirmed) {
            await approveSelectedRebatePayout();
        }
    } else {
        const result = await swalWithBootstrapButtons.fire({
            title: trans('public.Are you sure?'),
            text: trans('public.Reject all selected IB!'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: trans('public.Confirm'),
            cancelButtonText: trans('public.Cancel'),
            reverseButtons: true,
        });

        if (result.isConfirmed) {
            await rejectSelectedRebatePayout();
        }
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
                title: trans('public.Success'),
                text: response.data.message,
                icon: 'success',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
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
                title: trans('public.Error'),
                text: error.response.data.message,
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        } else {
            await Swal.fire({
                title: trans('public.Error'),
                text: trans('public.An error occurred while applying the rebate.'),
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        }
    }
}

async function rejectSelectedRebatePayout() {
    try {
        // Make the POST request using axios with selectedItems
        const response = await axios.post('/member/reject_rebate_payout', {
            selected_items: selectedItems.value,
            date: props.date,
            type: 'reject_selected',
        });

        if (response.data.success) {
            await Swal.fire({
                title: trans('public.Success'),
                text: response.data.message,
                icon: 'success',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
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
                title: trans('public.Error'),
                text: error.response.data.message,
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        } else {
            await Swal.fire({
                title: trans('public.Error'),
                text: trans('public.An error occurred while applying the rebate.'),
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: trans('public.OK'),
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
                {{ $t('public.Date') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.IB Name') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.IB Number') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.Account Type') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.Total Volume (LOTS)') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.Total Payout') }}
            </th>
            <th scope="col" class="px-6 py-3">
                {{ $t('public.Action') }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="lists.data.length === 0">
            <th colspan="8" class="py-4 text-lg text-center">
                {{ $t('public.No Pending') }}
            </th>
        </tr>
        <tr v-for="list in lists.data" :key="list.ib_account_types_id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th class="py-2 font-thin rounded-l-full">
                <Checkbox
                    :checked="selectAllChecked || isItemSelected(list.ib_account_types_id, list.date, list.meta_login, list.total_volume, list.total_revenue)"
                    @change="toggleItemCheckbox(list.ib_account_types_id, list.date, list.meta_login, list.total_volume, list.total_revenue)"
                />
            </th>
            <th>
                 {{ list.date }}
            </th>
            <th class="px-6 py-4">
                 {{ list.of_user.first_name }}
            </th>
            <th>
                 {{ list.of_user.ib_id }}
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
    <div class="flex justify-end gap-2 m-2">
        <Button
            v-if="showConfirmButton"
            variant="success"
            class="float-right text-xs"
            @click="confirmAction('approve')"
        >
            {{ $t('public.Confirm Approve') }}
        </Button>
        <Button
            v-if="showConfirmButton"
            variant="danger"
            class="float-right text-xs"
            @click="confirmAction('reject')"
        >
            {{ $t('public.Confirm Reject') }}
        </Button>
    </div>
</template>

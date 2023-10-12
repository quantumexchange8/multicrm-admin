<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import {computed, ref, watch, watchEffect} from "vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {usePage} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Action from "@/Pages/Member/RebatePayout/Action.vue";
import {transactionFormat} from "@/Composables/index.js";
import Checkbox from "@/Components/Checkbox.vue";
import Swal from "sweetalert2";
library.add(faSearch,faX,faRotateRight);

const props = defineProps({
    filters: Object,
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

watchEffect(() => {
    if (usePage().props.toast !== null) {
        refreshTable();
    }
});

const activeComponent = ref("pending"); // 'pending' is initially active

const setActiveComponent = async (component) => {
    activeComponent.value = component;

    await getResults();
};

const submitSearch = async () => {
    const dateRange = date.value.split(' ~ ');

    await getResults(1, dateRange, search.value);
};

const { getChannelName, formatDate, formatAmount, getStatusClass } = transactionFormat();

function clearField() {
    search.value = ''
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

function refreshTable() {
    getResults();
}

const payoutPending = ref({data: []});
const payoutHistories = ref({data: []});
const date = ref('');
const search = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1, dateRange, search = '') => {
    isLoading.value = true;
    try {
        let url = `/member/getPendingRebatePayout?page=${page}`;

        if (dateRange) {
            if (dateRange.length === 2) {
                const formattedDates = dateRange.map(date => `date[]=${date}`).join('&');
                url += `&${formattedDates}`;
            }
        }

        if (search) {
            console.log(search)
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        payoutPending.value = response.data.payoutPending;
        payoutHistories.value = response.data.payoutHistories;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
getResults();
const reset = () => {
    getResults();
    date.value = '';
    search.value = '';
}

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        const dateRange = date.value.split(' ~ ');
        getResults(currentPage.value, dateRange, search.value);
    }
};

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];

const selectAllChecked = ref(false);
const selectedItems = ref([]);

// Watch for changes in selectedItems array and update selectAllChecked accordingly
watch(selectedItems, () => {
    selectAllChecked.value = selectedItems.value.length === payoutPending.value.data.length;
});

function toggleAllCheckboxes() {
    if (selectAllChecked.value) {
        selectedItems.value = payoutPending.value.data.map((list) => ({
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

function toggleItemCheckbox(itemValue, closedDate, metaLogin, totalVolume, totalRevenue) {
    const existingIndex = selectedItems.value.findIndex(item =>
        item.ib_account_types_id === itemValue &&
        item.closed_date === closedDate &&
        item.meta_login === metaLogin &&
        item.total_volume === totalVolume &&
        item.total_revenue === totalRevenue
    );

    if (existingIndex !== -1) {
        // Item is already selected, remove it
        selectedItems.value.splice(existingIndex, 1);
    } else {
        // Item is not selected, add it
        selectedItems.value.push({
            ib_account_types_id: itemValue,
            closed_date: closedDate,
            meta_login: metaLogin,
            total_volume: totalVolume,
            total_revenue: totalRevenue,
        });
    }
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
    } else {
        const result = await swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: `Reject all selected IB!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
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
                getResults()
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
    <AuthenticatedLayout title="Trading Account Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Rebate Payout
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <InputIconWrapper>
                            <template #icon>
                                <font-awesome-icon
                                    icon="fa-solid fa-search"
                                    class="flex-shrink-0 w-5 h-5 cursor-pointer"
                                    aria-hidden="true"
                                />
                            </template>
                            <Input withIcon id="name" type="text" placeholder="Search by IB name or IB number" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                        </InputIconWrapper>
                        <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                            icon="fa-solid fa-x"
                            class="flex-shrink-0 w-3 h-3 cursor-pointer"
                            aria-hidden="true"
                            @click="clearField"
                        /></button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <vue-tailwind-datepicker
                            :formatter="formatter"
                            v-model="date"
                            input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center"
                        >
                            Search
                        </Button>
                        <Button
                            type="button"
                            variant="danger-opacity"
                            class="justify-center"
                            @click.prevent="reset"
                        >
                            Reset
                        </Button>
                    </div>

                </div>
                <div>
                    <div class="grid grid-cols-2 gap-4">
                        <Button
                            variant="primary-opacity"
                            class="px-6 border border-blue-800 justify-center mt-4 focus:ring-0"
                            :class="{ 'bg-transparent': activeComponent !== 'pending', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'pending' }"
                            @click="setActiveComponent('pending')"
                        >
                            Pending Payout
                        </Button>
                        <Button
                            variant="primary-opacity"
                            class="px-6 border border-blue-800 justify-center mt-4 focus:ring-0"
                            :class="{ 'bg-transparent': activeComponent !== 'history', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'history' }"
                            @click="setActiveComponent('history')"
                        >
                            Payout History
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div class="flex justify-end">
                <font-awesome-icon
                    icon="fa-solid fa-rotate-right"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-dark-eval-4"
                    aria-hidden="true"
                    @click="refreshTable"
                />
            </div>
            <div class="relative overflow-x-auto sm:rounded-lg">
                <!-- Payout Pending -->
                <div v-if="isLoading && activeComponent === 'pending'" class="w-full flex justify-center my-12">
                    <Loading />
                </div>
                <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400" v-if="activeComponent === 'pending'">
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
                            IB Number
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
                    <tr v-if="payoutPending.data.length === 0">
                        <th colspan="8" class="py-4 text-lg text-center">
                            No Pending
                        </th>
                    </tr>
                    <tr v-for="list in payoutPending.data" :key="list.ib_account_types_id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
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
                <div class="flex justify-end mt-4" v-if="activeComponent === 'pending'">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="payoutPending"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="handlePageChange"
                    />
                </div>
                <div class="flex justify-end gap-2 m-2">
                    <Button
                        v-if="showConfirmButton"
                        variant="success"
                        class="float-right text-xs"
                        @click="confirmAction('approve')"
                    >
                        Confirm Approve
                    </Button>
                    <Button
                        v-if="showConfirmButton"
                        variant="danger"
                        class="float-right text-xs"
                        @click="confirmAction('reject')"
                    >
                        Confirm Reject
                    </Button>
                </div>

                <!-- Payout History -->
                <div v-if="isLoading && activeComponent === 'history'" class="w-full flex justify-center my-12">
                    <Loading />
                </div>
                <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400" v-if="activeComponent === 'history'">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IB Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IB Number
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
                    <tr v-if="payoutHistories.data.length === 0">
                        <th colspan="8" class="py-4 text-lg text-center">
                            No History
                        </th>
                    </tr>
                    <tr v-for="rebateHistory in payoutHistories.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ rebateHistory.date }}
                        </th>
                        <th class="px-6 py-4">
                            {{ rebateHistory.of_user.first_name }}
                        </th>
                        <th>
                            {{ rebateHistory.of_user.ib_id }}
                        </th>
                        <th>
                            {{ rebateHistory.of_account_type.name }}
                        </th>
                        <th>
                            {{ rebateHistory.total_volume.toFixed(2) }}
                        </th>
                        <th>
                            {{ rebateHistory.total_revenue.toFixed(2) }}
                        </th>
                        <th class="px-6 py-2 font-thin rounded-r-full">
                            <Action
                                :list="rebateHistory"
                                :date="date"
                                status="approve"
                            />
                        </th>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4" v-if="activeComponent === 'history' && !isLoading">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="payoutHistories"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="handlePageChange"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

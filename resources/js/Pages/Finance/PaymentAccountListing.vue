<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {transactionFormat} from "@/Composables/index.js";
import {ref, watchEffect} from "vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import Action from "@/Pages/Finance/PaymentAccount/Action.vue";
import InputSelect from "@/Components/InputSelect.vue";
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "@/Components/Button.vue";
import Label from "@/Components/Label.vue";
import Loading from "@/Components/Loading.vue";
import {usePage} from "@inertiajs/vue3";
library.add(faSearch,faX,faRotateRight);

const props = defineProps({
    countries: Object
})

const { formatDate, formatAmount } = transactionFormat();
const paymentAccounts = ref({data: []});
const type = ref('bank');
const originalType = ref('bank');
const search = ref('');
const isLoading = ref(false);
const getResults = async (page = 1, type = '', search = '') => {
    isLoading.value = true;
    try {
        let url = `/finance/getPaymentAccount?page=${page}`;

        if (type) {
            url += `&type=${type}`;
        }

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        paymentAccounts.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults(1, 'bank');

function refreshTable() {
    getResults(1, type.value);
}

const submitSearch = async () => {
    await getResults(1, type.value, search.value);
    originalType.value = type.value;
};

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const reset = () => {
    type.value = 'bank'
    search.value = '';
    originalType.value = type.value;

    getResults(1, 'bank', search.value);
}

watchEffect(() => {
    if (usePage().props.toast !== null) {
        refreshTable();
    }
});

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];
</script>

<template>
    <AuthenticatedLayout title="Payment Account Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Payment Account Listing
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label>Payment Account Type</Label>
                    <InputSelect
                        class="block w-full text-sm"
                        v-model="type"
                    >
                        <option value="bank">Bank Account</option>
                        <option value="crypto">Cryptocurrency Wallet</option>
                    </InputSelect>
                </div>
                <div class="space-y-2">
                    <Label>Search By Name / Email / Account No</Label>
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
                            <Input withIcon id="name" type="text" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                        </InputIconWrapper>
                        <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                            icon="fa-solid fa-x"
                            class="flex-shrink-0 w-3 h-3 cursor-pointer"
                            aria-hidden="true"
                            @click.prevent="clearField"
                        /></button>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center py-3"
                        >
                            Search
                        </Button>
                        <Button
                            variant="danger-opacity"
                            class="justify-center py-3"
                            @click.prevent="reset"
                        >
                            Reset
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div class="flex justify-between mb-4">
                <div class="flex gap-6">
                    <span class="flex items-center text-sm font-medium text-gray-900 dark:text-white"><span class="flex w-5 h-5 bg-green-500 dark:bg-[#013B20] rounded-full mr-1.5 flex-shrink-0"></span>Active</span>
                    <span class="flex items-center text-sm font-medium text-gray-900 dark:text-white"><span class="flex w-5 h-5 bg-red-500 dark:bg-[#4C1310] rounded-full mr-1.5 flex-shrink-0"></span>Inactive</span>
                </div>
                <font-awesome-icon
                    icon="fa-solid fa-rotate-right"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-dark-eval-4"
                    aria-hidden="true"
                    @click="refreshTable"
                />
            </div>
            <div v-if="isLoading" class="w-full flex justify-center">
                <Loading />
            </div>
            <div v-else class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Email
                        </th>
                        <th v-if="originalType === 'bank'" scope="col" class="px-4 py-3">
                            Bank Name
                        </th>
                        <th v-else-if="originalType === 'crypto'" scope="col" class="px-4 py-3">
                            USDT Protocol Type
                        </th>
                        <th v-if="originalType === 'bank'" scope="col" class="px-4 py-3">
                            Bank Holder Name
                        </th>
                        <th v-if="originalType === 'bank'" scope="col" class="px-4 py-3">
                            Bank Account No
                        </th>
                        <th v-else-if="originalType === 'crypto'" scope="col" class="px-4 py-3">
                            Token Address
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="account in paymentAccounts.data" class="bg-white even:dark:bg-transparent odd:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ account.user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ account.user.email }}
                        </th>
                        <th>
                            {{ account.payment_platform_name }}
                        </th>
                        <th v-if="originalType === 'bank'">
                            {{ account.payment_account_name }}
                        </th>
                        <th>
                            {{ account.account_no }}
                        </th>
                        <th>
                            <span v-if="account.status === 1" class="flex w-5 h-5 bg-green-500 dark:bg-[#013B20] mx-auto rounded-full"></span>
                            <span v-else-if="account.status === 0" class="flex w-5 h-5 bg-red-500 dark:bg-[#4C1310] mx-auto rounded-full"></span>
                        </th>
                        <th class="px-6 py-2 font-thin rounded-r-full">
                            <Action
                                :account="account"
                                :countries="countries"
                                :bankProof="account.media"
                            />
                        </th>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="paymentAccounts"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="getResults"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

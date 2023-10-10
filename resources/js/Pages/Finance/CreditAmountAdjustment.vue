<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Badge from "@/Components/Badge.vue";
import {transactionFormat} from "@/Composables/index.js";
import {ref, watchEffect} from "vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import Action from "@/Pages/Finance/CreditAdjustment/Action.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputSelect from "@/Components/InputSelect.vue";
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "@/Components/Button.vue";
import Label from "@/Components/Label.vue";
import Loading from "@/Components/Loading.vue";
import {usePage} from "@inertiajs/vue3";
library.add(faSearch,faX,faRotateRight);

const { formatDate, formatAmount } = transactionFormat();
const tradingAccounts = ref({data: []});
const search = ref('');
const isLoading = ref(false);
const getResults = async (page = 1, search = '') => {
    isLoading.value = true;
    try {
        let url = `/finance/getTradingAccounts?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        tradingAccounts.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

function refreshTable() {
    getResults();
}

const submitSearch = async () => {

    await getResults(1, search.value);
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
    search.value = '';

    getResults();
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
    <AuthenticatedLayout :title="$t('public.Credit Amount Adjustment')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Credit Amount Adjustment') }}
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2 col-span-2">
                    <Label>{{ $t('public.Search By Name / Email / Account No') }}</Label>
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
                            @click="clearField"
                        /></button>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center py-3"
                        >
                            {{ $t('public.Search') }}
                        </Button>
                        <Button
                            variant="danger-opacity"
                            class="justify-center py-3"
                            @click.prevent="reset"
                        >
                            {{ $t('public.Reset') }}
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
            <div v-if="isLoading" class="w-full flex justify-center">
               <Loading />
            </div>
            <div v-else class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Name') }}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Email') }}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Upline Email')}}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Account Number') }}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Balance') }} (USD)
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Credit') }} (USD)
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="account in tradingAccounts.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ account.of_user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ account.of_user.email }}
                        </th>
                        <th>
                            {{ account.of_user.upline ? account.of_user.upline.email : '' }}
                            <span v-if="!account.of_user.upline" class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-500 dark:text-purple-100 uppercase">No Upline</span>
                        </th>
                        <th>
                            {{ account.meta_login }}
                        </th>
                        <th>
                            {{ formatAmount(account.balance) }}
                        </th>
                        <th>
                            {{ formatAmount(account.credit) }}
                        </th>
                        <th class="px-6 py-2 font-thin rounded-r-full">
                            <Action
                                :account="account"
                            />
                        </th>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="tradingAccounts"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="getResults"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

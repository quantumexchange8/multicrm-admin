<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {computed, ref, watch, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import { faSearch,faX,faRotateRight } from '@fortawesome/free-solid-svg-icons';
import { library } from "@fortawesome/fontawesome-svg-core";
import IbListing from "@/Pages/Member/RebateAllocation/IbListing.vue";
import InputSelect from "@/Components/InputSelect.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Action from "@/Pages/Member/RebateAllocation/Action.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "@/Components/Button.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Loading from "@/Components/Loading.vue";

library.add(faSearch,faX,faRotateRight);
const page = usePage()
const user = computed(() => page.props.auth.user)

const props = defineProps({
    getAccountTypeSel: Object,
    get_ibs_sel: Object,
});

function refreshTable() {
    getResults();
}

watchEffect(() => {
    if (usePage().props.toast !== null) {
        refreshTable();
    }
});

const account_type = ref(1);
const getIbListing = ref({data: []});
const defaultAccountSymbolGroup = ref();
const directIb = ref();
const directMember = ref();
const totalIb = ref();
const totalMember = ref();
const date = ref('');
const search = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1, account_type = 1, search = '') => {
    isLoading.value = true;
    try {
        let url = `/member/getIbListing?page=${page}`;

        if (account_type) {
            url += `&account_type=${account_type}`;
        }

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        getIbListing.value = response.data.getIbListing;
        defaultAccountSymbolGroup.value = response.data.defaultAccountSymbolGroup;
        directIb.value = response.data.directIb;
        directMember.value = response.data.directMember;
        totalIb.value = response.data.totalIb;
        totalMember.value = response.data.totalMember;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
getResults();

watch(account_type, (account_type) => {
    getResults(1, account_type, search.value);
});

const submitSearch = async () => {
    await getResults(1, account_type.value, search.value);
};

function resetField() {
    getResults();
    account_type.value = 1;
    search.value = '';
}

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        getResults(currentPage.value, account_type.value, search.value);
    }
};

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];

const accountType = computed(() => {
    const selectedType = props.getAccountTypeSel[account_type.value];

    return {
        name: selectedType || 'Undefined Type',
        directIb: directIb.value || 0,
        directMember: directMember.value || 0,
        totalIb: totalIb.value || 0,
        totalMember: totalMember.value || 0,
    };
});

</script>

<template>

<AuthenticatedLayout title="Rebate Allocation">
    <template #header>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                Rebate Allocation
            </h2>
        </div>
    </template>

    <div class="flex flex-col space-y-2 mb-6">
        <Label>Account Type</Label>
        <InputSelect
            class="block w-1/4 text-sm"
            v-model="account_type"
        >
            <option v-for="(name, accountTypeId) in getAccountTypeSel" :value="accountTypeId">{{ name }}</option>
        </InputSelect>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
            <div class="flex flex-col items-center mb-4">
                <img
                    class="h-12 w-12 rounded-full"
                    :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                    alt="ProfilePic"
                >
                <h5 class="my-1 text-xl font-medium text-gray-900 dark:text-white">{{ 'Current Tech' }}</h5>
                <span class="text-sm text-gray-500 dark:text-gray-400">IB00000</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center md:text-left">
                <div class="text-black dark:text-dark-eval-3">Account type:</div>
                <div class="text-black dark:text-white">{{ accountType.name }}</div>

                <div class="text-black dark:text-dark-eval-3">Since Date </div>
                <div class="text-black dark:text-white">{{ '01-09-2023' }}</div>

                <div class="text-black dark:text-dark-eval-3">Direct IB </div>
                <div class="text-black dark:text-white">{{ accountType.directIb }}</div>

                <div class="text-black dark:text-dark-eval-3">Direct Clients </div>
                <div class="text-black dark:text-white">{{ directMember }}</div>

                <div class="text-black dark:text-dark-eval-3">Total Group IB </div>
                <div class="text-black dark:text-white">{{ totalIb }}</div>

                <div class="text-black dark:text-dark-eval-3">Total Group Clients </div>
                <div class="text-black dark:text-white">{{ totalMember }}</div>
            </div>
        </div>
        <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
            <div class="flex flex-col text-center md:text-left">
                <h5 class="mb-6 text-xl font-medium text-gray-900 dark:text-white">My Rebate Info</h5>
                <div v-if="isLoading" class="w-full flex justify-center my-12">
                    <Loading />
                </div>
                <div
                    v-else
                    v-for="defaultIB in defaultAccountSymbolGroup"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm my-2"
                >
                    <div class="dark:text-dark-eval-3 uppercase">
                        {{ defaultIB.symbol_group.name }} (USD) / LOT
                    </div>
                    <div class="text-center">
                        {{ defaultIB.amount }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full my-6 flex justify-end gap-4">
        <div class="relative w-full md:w-2/3">
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
                <Input withIcon id="name" type="text" placeholder="Name / Email" class="block w-full" v-model="search" @keydown="handleKeyDown" />
            </InputIconWrapper>
            <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                icon="fa-solid fa-x"
                class="flex-shrink-0 w-3 h-3 cursor-pointer"
                aria-hidden="true"
                @click="clearField"
            /></button>
        </div>
        <div>
            <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                <Button
                    variant="primary-opacity"
                    class="justify-center py-2.5"
                    @click="submitSearch"
                >
                    Search
                </Button>
                <Button
                    variant="danger-opacity"
                    class="justify-center py-2.5"
                    @click.prevent="resetField"
                >
                    Reset
                </Button>
            </div>
        </div>
    </div>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <div v-if="isLoading" class="w-full flex justify-center my-12">
                <Loading />
            </div>
            <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr class="uppercase">
                    <th scope="col" class="px-6 py-3">
                        IB Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IB Number
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Current Upline
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="getIbListing.data.length === 0">
                    <th colspan="8" class="py-4 text-lg text-center">
                        No IB
                    </th>
                </tr>
                <tr v-for="ib in getIbListing.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                        {{ ib.of_user.first_name }}
                    </th>
                    <th class="px-6 py-4">
                        {{ ib.of_user.ib_id }}
                    </th>
                    <th>
                        {{ ib.of_user.upline ? ib.of_user.upline.first_name : '' }}
                        <span v-if="!ib.of_user.upline" class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-500 dark:text-purple-100 uppercase">No Upline</span>
                    </th>
                    <th>
                        {{ ib.of_user.direct_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.direct_client }}
                    </th>
                    <th>
                        {{ ib.of_user.total_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.total_client }}
                    </th>
                    <th class="px-6 py-4 space-x-2 font-thin rounded-r-full">
                        <Action
                            :ib="ib"
                            :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
                            :get_ibs_sel="get_ibs_sel"
                        />
                    </th>
                </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <TailwindPagination
                    :item-classes=paginationClass
                    :active-classes=paginationActiveClass
                    :data="getIbListing"
                    :limit=1
                    :keepLength="true"
                    @pagination-change-page="handlePageChange"
                />
            </div>
        </div>
    </div>

</AuthenticatedLayout>


</template>

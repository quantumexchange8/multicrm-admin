<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Label from "@/Components/Label.vue";
import {ref, watch, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Action from "@/Pages/Setting/MasterSetting/Action.vue";
import {transactionFormat} from "@/Composables/index.js";
library.add(faSearch,faX,faRotateRight);
function refreshTable() {
    getResults();
}
const { formatCategory } = transactionFormat();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        refreshTable();
    }
});

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const masterSettings = ref({data: []});
const search = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

watch(search, () => {
    getResults(1, search.value);
});

const getResults = async (page = 1, search = '') => {
    isLoading.value = true;
    try {
        let url = `/setting/getMasterSetting?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        masterSettings.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
getResults();
const reset = () => {
    getResults();
    search.value = '';
}

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        const dateRange = date.value.split(' ~ ');
        getResults(currentPage.value, search.value);
    }
};

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] !font-bold text-xs'
];
</script>

<template>
    <AuthenticatedLayout :title="$t('public.sidebar.Master Setting')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.sidebar.Master Setting') }}
                </h2>
            </div>
        </template>

        <form>
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label>{{ $t('public.Search') }}</Label>
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
                        <button type="button" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                            icon="fa-solid fa-x"
                            class="flex-shrink-0 w-3 h-3 cursor-pointer"
                            aria-hidden="true"
                            @click="clearField"
                        /></button>
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
                <div v-if="isLoading" class="w-full flex justify-center my-12">
                    <Loading />
                </div>
                <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Name') }}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Value') }}
                        </th>
                        <th scope="col" class="px-4 py-3 w-56 text-center">
                            {{ $t('public.Action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="masterSettings.data.length === 0">
                        <th colspan="8" class="py-4 text-lg text-center">
                            {{ $t('public.No Data') }}
                        </th>
                    </tr>
                    <tr v-for="setting in masterSettings.data" :key="setting.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white">
                        <th scope="row" class="p-4 font-thin rounded-l-full">
                            {{ formatCategory(setting.name) }}
                        </th>
                        <th class="px-6 py-4">
                            {{ setting.value }}
                        </th>
                        <th class="py-2 font-thin rounded-r-full">
                            <Action
                                :setting="setting"
                            />
                        </th>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="masterSettings"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="handlePageChange"
                    />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

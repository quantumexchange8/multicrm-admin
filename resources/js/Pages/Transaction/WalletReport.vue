<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import InputSelect from "@/Components/InputSelect.vue";
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";
import Label from "@/Components/Label.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {useForm} from "@inertiajs/vue3";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
library.add(faSearch,faX,faRotateRight);
import { router } from '@inertiajs/vue3'
import toast from "@/Composables/toast.js";
import {transactionFormat} from "@/Composables/index.js";
import Paginator from "@/Components/Paginator.vue";
import Action from "@/Pages/Transaction/Wallet/Action.vue";
const { formatAmount } = transactionFormat();

const props = defineProps({
    users: Object,
    filters: Object
})

async function refreshTable() {
    await router.visit('/transaction/wallet_report', { preserveScroll: true, preserveState: true, onFinish: addToast});
}

function addToast() {
    toast.add({
        message: "Table Refreshed!",
    });
}

const form = useForm({
    search: props.filters.search,
    role: props.filters.role
})

const submitSearch = () => {
    form.get(route('transaction.wallet_report'), {
        preserveScroll: true,
        preserveState: true,
    })
};

function clearField() {
    form.search = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const reset = () => {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('role');
    url.searchParams.delete('page');

    // Navigate to the updated URL without the search parameter
    window.location.href = url.href;
}
</script>

<template>
    <AuthenticatedLayout title="All Wallet Report">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    All Wallet Report
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="space-y-2">
                    <Label>Filter By</Label>
                    <InputSelect
                        class="block w-full text-sm"
                        v-model="form.role"
                        placeholder="All"
                    >
                        <option value="ib">With Rebate Wallet</option>
                        <option value="member">Without Rebate Wallet</option>
                    </InputSelect>
                </div>
                <div class="space-y-2 col-span-2">
                    <Label>Search By Name / Email</Label>
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
                            <Input withIcon id="name" type="text" class="block w-full" v-model="form.search" @keydown="handleKeyDown" />
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
                            :disabled="form.processing"
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
            <div class="flex justify-end">
                <font-awesome-icon
                    icon="fa-solid fa-rotate-right"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-dark-eval-4"
                    aria-hidden="true"
                    @click="refreshTable"
                />
            </div>
            <div class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Cash Wallet (USD)
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Rebate Wallet (USD)
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users.data" :key="user.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ user.email }}
                        </th>
                        <th>
                            $ {{ formatAmount(user.cash_wallet) }}
                        </th>
                        <th>
                            <span v-if="user.user_ib">
                                $ {{ formatAmount(user.user_ib.rebate_wallet ) }}
                            </span>
                            <span v-else>-</span>
                        </th>
                        <th class="px-6 py-2 font-thin rounded-r-full">
                            <Action
                                :user="user"
                            />
                        </th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <Paginator :links="props.users.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

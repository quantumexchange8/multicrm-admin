<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Label from "@/Components/Label.vue";
import InputSelect from "@/Components/InputSelect.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {ref} from "vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";
import {useForm} from "@inertiajs/vue3";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
library.add(faSearch,faX,faRotateRight);
import { router } from '@inertiajs/vue3'
import toast from "@/Composables/toast.js";
import WithdrawalPending from "@/Pages/Transaction/Withdrawal/WithdrawalPending.vue";
import WithdrawalHistory from "@/Pages/Transaction/Withdrawal/WithdrawalHistory.vue";

const props = defineProps({
    withdrawals: Object,
    histories: Object,
    filters: Object
})

async function refreshTable() {
    await router.visit('/transaction/withdrawal_report', { preserveScroll: true, preserveState: true, onFinish: addToast});
}

function addToast() {
    toast.add({
        message: "Table Refreshed!",
    });
}

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const activeComponent = ref("pending"); // 'pending' is initially active

const setActiveComponent = (component) => {
    activeComponent.value = component;
};

const form = useForm({
    search: props.filters.search,
    date: props.filters.date,
    type: props.filters.type
})

const submitSearch = () => {
    form.get(route('transaction.withdrawal_report'), {
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
    url.searchParams.delete('type');
    url.searchParams.delete('date%5B%5D');
    url.searchParams.delete('date[]');
    url.searchParams.delete('page');

    // Navigate to the updated URL without the search parameter
    window.location.href = url.href;
}

</script>

<template>
    <AuthenticatedLayout title="Withdrawal Report">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Withdrawal Report
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label>Filter By Withdrawal Method</Label>
                    <InputSelect
                        class="block w-full text-sm"
                        v-model="form.type"
                        placeholder="All"
                    >
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                    </InputSelect>
                </div>
                <div class="space-y-2">
                    <Label>Filter By Date</Label>
                    <vue-tailwind-datepicker
                        :formatter="formatter"
                        v-model="form.date"
                        input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                    />
                </div>
                <div class="space-y-2">
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
                <div>
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center"
                            :disabled="form.processing"
                        >
                            Search
                        </Button>
                        <Button
                            variant="danger-opacity"
                            class="justify-center"
                            @click.prevent="reset"
                        >
                            Reset
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-2 gap-4 w-full md:w-1/2">
            <Button
                variant="primary-opacity"
                class="px-6 border border-blue-800 justify-center mt-4 focus:ring-0"
                :class="{ 'bg-transparent': activeComponent !== 'pending', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'pending' }"
                @click="setActiveComponent('pending')"
            >
                Pending Transaction
            </Button>
            <Button
                variant="primary-opacity"
                class="px-6 border border-blue-800 justify-center mt-4 focus:ring-0"
                :class="{ 'bg-transparent': activeComponent !== 'history', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'history' }"
                @click="setActiveComponent('history')"
            >
                Transaction History
            </Button>
        </div>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div class="relative overflow-x-auto sm:rounded-lg">
                <WithdrawalPending
                    v-if="activeComponent === 'pending'"
                    :withdrawals="withdrawals"
                />
                <WithdrawalHistory
                    v-if="activeComponent === 'history'"
                    :histories="histories"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

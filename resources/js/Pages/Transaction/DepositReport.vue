<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Paginator from "@/Components/Paginator.vue";
import Badge from "@/Components/Badge.vue";
import Label from "@/Components/Label.vue";
import InputSelect from "@/Components/InputSelect.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {ref} from "vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";
import {useForm} from "@inertiajs/vue3";
import {faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
library.add(faSearch,faX);

const props = defineProps({
    deposits: Object,
    filters: Object
})

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const form = useForm({
    search: props.filters.search,
    date: props.filters.date,
    type: props.filters.type
})

const submitSearch = () => {
    form.get(route('transaction.deposit_report'), {
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

function getStatusClass(status) {
    if (status === 'Successful') {
        return 'success';
    } else if (status === 'Submitted') {
        return 'warning';
    } else if (status === 'Rejected') {
        return 'danger';
    } else {
        return ''; // Default case or handle other statuses
    }
}

function formatDate(date) {
    const formattedDate = new Date(date).toISOString().slice(0, 10);
    return formattedDate.replace(/-/g, '/');
}

</script>

<template>
    <AuthenticatedLayout title="Trading Account Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Deposit Report
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label>Filter By Deposit Type</Label>
                    <InputSelect
                        class="block w-full text-sm"
                        v-model="form.type"
                        placeholder="All"
                    >
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                        <option value="fpx">FPX</option>
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
                            <Input withIcon id="name" type="text" placeholder="Search by IB name or IB number" class="block w-full" v-model="form.search" @keydown="handleKeyDown" />
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

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
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
                            Date
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Deposit Method
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Deposit Amount
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Payment Charges
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="deposit in deposits.data" :key="deposit.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ deposit.of_user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ deposit.of_user.email }}
                        </th>
                        <th>
                            {{ formatDate(deposit.created_at) }}
                        </th>
                        <th>
                            {{ deposit.channel }}
                        </th>
                        <th>
                            $ {{ deposit.amount }}
                        </th>
                        <th>
                            {{ deposit.payment_charges }}
                        </th>
                        <th class="px-6 py-2 font-thin rounded-r-full">
                            <Badge :status="getStatusClass(deposit.status)">{{ deposit.status }}</Badge>
                        </th>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <Paginator :links="props.deposits.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

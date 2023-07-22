<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import { faSearch,faX } from '@fortawesome/free-solid-svg-icons';
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref, watch } from "vue";
import { router } from '@inertiajs/vue3'
import debounce from "lodash/debounce.js";

library.add(faSearch,faX);

    defineProps({
        ibs: Object,
    });

    let search = ref('');

    watch(search, debounce(function  (value) {
        router.get('/ib/ib_listing', { search: value }, { preserveState:true, replace:true });
    }, 300));

    function clearField() {
        search.value = '';
    }

    function formatDate(date) {
        return new Date(date).toISOString().slice(0, 10);
    }

</script>


<template>
    <AuthenticatedLayout title="IB Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    IB Listing
                </h2>
            </div>
        </template>

        <div class="w-full md:w-1/3 float-right mb-6">
            <div class="relative">
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
                    <Input withIcon id="name" type="text" placeholder="Name / Email" class="block w-full" v-model="search" />
                </InputIconWrapper>
                <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                    icon="fa-solid fa-x"
                    class="flex-shrink-0 w-3 h-3 cursor-pointer"
                    aria-hidden="true"
                    @click="clearField"
                /></button>
            </div>

        </div>
        

        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            NAME
                        </th>
                        <th scope="col" class="px-6 py-3">
                            EMAIL
                        </th>
                        <th scope="col" class="px-6 py-3">
                            REGISTER DATE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            WALLET BALANCE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL ACCOUNT
                        </th>
                        <th scope="col" class="px-6 py-3">
                            COUNTRY
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACTIONS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ib in ibs" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ ib.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ ib.email }}
                        </th>
                        <th>
                            {{ formatDate(ib.created_at) }}
                        </th>
                        <th>
                            {{ ib.cash_wallet }}
                        </th>
                        <th>
                            {{ ib.trading_accounts.length }}
                        </th>
                        <th>
                            {{ ib.country }}
                        </th>
                        <th>
                            <button @click="ViewUser">View</button>
                            <button @click="ResetPass">Reset</button>
                            <button @click="Destroy">Delete</button>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>

    </AuthenticatedLayout>
</template>
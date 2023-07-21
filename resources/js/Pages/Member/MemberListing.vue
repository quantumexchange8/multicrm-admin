<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'

    defineProps({
        members: Object,
    });

    function formatDate(date) {
        return new Date(date).toISOString().slice(0, 10);
    }

</script>

<template>

    <AuthenticatedLayout title="Member Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Member Listing
                </h2>
            </div>
        </template>

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
                    <tr v-for="member in members" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ member.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ member.email }}
                        </th>
                        <th>
                            {{ formatDate(member.created_at) }}
                        </th>
                        <th>
                            {{ member.cash_wallet }}
                        </th>
                        <th>
                            {{ member.trading_accounts.length }}
                        </th>
                        <th>
                            {{ member.country }}
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
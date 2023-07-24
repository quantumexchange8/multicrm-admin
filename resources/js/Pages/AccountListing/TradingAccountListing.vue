<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'

    defineProps({
        tradingAccs: Object
    });

    function formatDate(date) {
        return new Date(date).toISOString().slice(0, 10);
    }

</script>

<template>

    <AuthenticatedLayout title="Trading Account Listing">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Trading Account Listing
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
                            ACCOUNT NO.
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACCOUNT TYPE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            REGISTER DATE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            BALANCE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            EQUITY
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CREDIT
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACTIONS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="tradingAcc in tradingAccs" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ tradingAcc.of_user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ tradingAcc.meta_login }}
                        </th>
                        <th>
                            {{ tradingAcc.account_type.name }}
                        </th>
                         <th>
                            {{ formatDate(tradingAcc.created_at) }}
                        </th>
                        <th>
                            {{ tradingAcc.balance }}
                        </th>
                        <th>
                            {{ tradingAcc.equity }}
                        </th>
                        <th>
                            {{ tradingAcc.credit }}
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
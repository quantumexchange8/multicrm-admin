<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'

defineProps({
    totalDeposit: [String, Number],
    totalWithdrawal: [String, Number],
})

</script>

<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Dashboard
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

            <fieldset
                class="border-2 border-[#05C46B] p-4 rounded-lg shadow-md text-center bg-gradient-to-b from-transparent to-[#05c46b33]"
            >
                <legend class="text-xl px-4 uppercase font-semibold">Total Deposit {{ $page.totalApprovedDeposit}}</legend>
                <p class="text-4xl">$ {{ totalDeposit }}</p>
            </fieldset>

            <fieldset
                class="border-2 border-[#FF3F34] p-4 rounded-lg shadow-md text-center bg-gradient-to-b from-transparent to-[#ff3f3433]"
            >
                <legend class="text-xl px-4 uppercase font-semibold">Total Withdrawal</legend>
                <p class="text-4xl">$ {{ totalWithdrawal }}</p>
            </fieldset>

        </div>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            You're logged in!
        </div>
    </AuthenticatedLayout>
</template>

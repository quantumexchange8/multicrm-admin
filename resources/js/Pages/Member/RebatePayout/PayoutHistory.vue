<script setup>
import Action from "@/Pages/Member/RebatePayout/Action.vue";
import Paginator from "@/Components/Paginator.vue";

const props = defineProps({
    rebatePayoutHistory: Object,
    date: String
})
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
        <tr>
            <th scope="col" class="px-6 py-3">
                Date
            </th>
            <th scope="col" class="px-6 py-3">
                IB Name
            </th>
            <th scope="col" class="px-6 py-3">
                Account Number
            </th>
            <th scope="col" class="px-6 py-3">
                Account Type
            </th>
            <th scope="col" class="px-6 py-3">
                Total Volume (LOTS)
            </th>
            <th scope="col" class="px-6 py-3">
                Total Payout
            </th>
            <th scope="col" class="px-6 py-3">
                Action
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="rebatePayoutHistory.data.length === 0">
            <th colspan="8" class="py-4 text-lg text-center">
                No History
            </th>
        </tr>
        <tr v-for="rebateHistory in rebatePayoutHistory.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                {{ rebateHistory.date }}
            </th>
            <th class="px-6 py-4">
                {{ rebateHistory.of_user.first_name }}
            </th>
            <th>
                {{ rebateHistory.meta_login }}
            </th>
            <th>
                {{ rebateHistory.of_account_type.name }}
            </th>
            <th>
                {{ rebateHistory.total_volume.toFixed(2) }}
            </th>
            <th>
                {{ rebateHistory.total_revenue.toFixed(2) }}
            </th>
            <th class="px-6 py-2 font-thin rounded-r-full">
                <Action
                    :list="rebateHistory"
                    :date="date"
                    status="approve"
                />
            </th>
        </tr>
        </tbody>
    </table>
    <div class="flex justify-end mt-4">
        <Paginator :links="props.rebatePayoutHistory.links" />
    </div>
</template>

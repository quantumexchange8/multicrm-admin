<script setup>
import Action from "@/Pages/Transaction/Withdrawal/Action.vue";
import Paginator from "@/Components/Paginator.vue";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    withdrawals: Object
})

const { getChannelName, formatDate, formatAmount } = transactionFormat();
</script>

<template>
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
                Withdrawal Method
            </th>
            <th scope="col" class="px-4 py-3">
                Withdrawal Amount
            </th>
            <th scope="col" class="px-4 py-3">
                Payment Charges
            </th>
            <th scope="col" class="px-4 py-3 w-56">
                Action
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="withdrawals.data.length === 0">
            <th colspan="8" class="py-4 text-lg text-center">
                No Pending
            </th>
        </tr>
        <tr v-for="withdrawal in withdrawals.data" :key="withdrawal.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                {{ withdrawal.of_user.first_name }}
            </th>
            <th class="px-6 py-4">
                {{ withdrawal.of_user.email }}
            </th>
            <th>
                {{ formatDate(withdrawal.created_at) }}
            </th>
            <th>
                {{ getChannelName(withdrawal.channel) }}
            </th>
            <th>
                $ {{ formatAmount(withdrawal.amount) }}
            </th>
            <th>
                {{ withdrawal.payment_charges ?? '-' }}
            </th>
            <th class="py-2 font-thin rounded-r-full">
                <Action
                    :withdrawal="withdrawal"
                />
            </th>
        </tr>
        </tbody>
    </table>
    <div class="flex justify-end mt-4">
        <Paginator :links="props.withdrawals.links" />
    </div>
</template>

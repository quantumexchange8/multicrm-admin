<script setup>
import Action from "@/Pages/Transaction/Withdrawal/Action.vue";
import Paginator from "@/Components/Paginator.vue";
import {transactionFormat} from "@/Composables/index.js";
import Badge from "@/Components/Badge.vue";

const props = defineProps({
    histories: Object
})

const { getChannelName, formatDate, getStatusClass, formatAmount } = transactionFormat();
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
        <tr>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Name') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Email') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Date') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Withdrawal Method') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Withdrawal Amount') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Payment Charges') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Status') }}
            </th>
            <th scope="col" class="px-4 py-3">
                {{ $t('public.Action') }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="histories.data.length === 0">
            <th colspan="8" class="py-4 text-lg text-center">
                {{ $t('public.No Pending') }}
            </th>
        </tr>
        <tr v-for="history in histories.data" :key="history.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                {{ history.of_user.first_name }}
            </th>
            <th class="px-6 py-4">
                {{ history.of_user.email }}
            </th>
            <th>
                {{ formatDate(history.created_at) }}
            </th>
            <th>
                {{ $t('public.' + getChannelName(history.channel)) }}
            </th>
            <th>
                $ {{ formatAmount(history.amount) }}
            </th>
            <th>
                {{ history.payment_charges ?? '-' }}
            </th>
            <th>
                <Badge :status="getStatusClass(history.status)">{{ $t('public.' + history.status) }}</Badge>
            </th>
            <th class="py-2 font-thin rounded-r-full">
                <Action
                    :withdrawal="history"
                    type="history"
                />
            </th>
        </tr>
        </tbody>
    </table>
    <div class="flex justify-end mt-4">
        <Paginator :links="props.histories.links" />
    </div>
</template>

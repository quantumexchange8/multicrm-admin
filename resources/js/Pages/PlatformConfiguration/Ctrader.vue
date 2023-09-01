<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {ref, watchEffect} from "vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Loading from "@/Components/Loading.vue";
import {transactionFormat} from "@/Composables/index.js";
import Action from "@/Pages/PlatformConfiguration/Ctrader/Action.vue";
import AddAccountType from "@/Pages/PlatformConfiguration/Ctrader/AddAccountType.vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    leverages: Object,
    groups: Object,
})

const { formatType } = transactionFormat();
const cTraderAccounts = ref({data: []});
const isLoading = ref(false);
const getResults = async (page = 1) => {
    isLoading.value = true;
    try {
        let url = `/platform_configuration/getCTraderAccounts?page=${page}`;

        const response = await axios.get(url);
        cTraderAccounts.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults()

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];

const formattedTradeOpenDuration = (trade_open_duration) => {
    if (trade_open_duration > 60) {
        return `${Math.round(trade_open_duration / 60)} min`;
    } else {
        return `${trade_open_duration} s`;
    }
};
</script>

<template>
    <AuthenticatedLayout title="cTrader Platform Configuration">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    cTrader Platform Configuration
                </h2>
            </div>
        </template>

        <div class="flex justify-end">
            <AddAccountType
                :leverages="props.leverages"
                :groups="props.groups"
            />
        </div>

        <div class="p-4 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div v-if="isLoading" class="w-full flex justify-center mt-8">
                <Loading />
            </div>
            <div v-else class="relative overflow-x-auto sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            Account Type
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Group Name
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Min Deposit
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Leverage
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Currency
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Allow New Account Creation
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Commission Structure
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Trade Open Duration
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="account in cTraderAccounts.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="p-4 font-thin rounded-l-full">
                            {{ account.name }}
                        </th>
                        <td class="p-4">
                            {{ account.meta_group.display }}
                        </td>
                        <td class="p-4">
                            {{ account.minimum_deposit }}
                        </td>
                        <td class="p-4">
                            1:{{ account.leverage }}
                        </td>
                        <td class="p-4">
                            {{ account.currency }}
                        </td>
                        <td class="p-4">
                            {{ account.allow_create_account === -1 ? 'No' : 'Yes' }}
                        </td>
                        <td class="p-4">
                            {{ formatType(account.commission_structure) }}
                        </td>
                        <td class="p-4">
                            {{ formattedTradeOpenDuration(account.trade_open_duration) }}
                        </td>
                        <td class="p-4 rounded-r-full">
                            <Action
                                :account="account"
                                :leverages="leverages"
                                :groups="groups"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="cTraderAccounts"
                        :limit=1
                        :keepLength="true"
                        @pagination-change-page="getResults"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

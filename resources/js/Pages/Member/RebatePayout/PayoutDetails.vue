<script setup>
const props = defineProps({
    list: Object,
    rebatePayoutDetails: Object
})


</script>

<template>
    <h2
        class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
    >
        View Rebate Payout Details
    </h2>
    <hr>

    <h3
        class="text-lg font-medium mb-2 text-gray-900 dark:text-dark-eval-3 mt-8"
    >
        IB: {{ list.of_user.first_name }}
    </h3>

    <div class="relative overflow-x-auto sm:rounded-lg my-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
            <tr class="uppercase">
                <th scope="col" class="px-6 py-3">
                    Client Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Trading Account
                </th>
                <th scope="col" class="px-6 py-3">
                    Account Platform
                </th>
                <th scope="col" class="px-6 py-3">
                    Volume (Lot)
                </th>
                <th scope="col" class="px-6 py-3">
                    Rebate / Lot
                </th>
                <th scope="col" class="px-6 py-3">
                    Rebate Earned
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="rebatePayoutDetail in rebatePayoutDetails" class="bg-white odd:dark:bg-dark-eval-0 even:dark:bg-transparent text-xs font-thin text-gray-900 dark:text-white text-center">
                <td class="px-6 py-4 font-thin rounded-l-full">
                    {{ rebatePayoutDetail.of_ticket_user.first_name }}
                </td>
                <td>
                    {{ rebatePayoutDetail.meta_login }}
                </td>
                <td>
                    {{ rebatePayoutDetail.of_account_type.name }}
                </td>
                <td>
                    {{ rebatePayoutDetail.total_volume.toFixed(2) }}
                </td>
                <td>
                    $ {{ rebatePayoutDetail.final_net_rebate }}
                </td>
                <td class="px-6 py-4 font-thin rounded-r-full">
                    $ {{ rebatePayoutDetail.total_revenue.toFixed(2) }}
                </td>
            </tr>
            </tbody>
        </table>
        <h3
            class="text-lg font-medium text-gray-900 dark:text-gray-100 float-right mr-2 mt-4"
        >
            Total Payout: $ {{ list.total_revenue.toFixed(2) }}
        </h3>
    </div>

</template>

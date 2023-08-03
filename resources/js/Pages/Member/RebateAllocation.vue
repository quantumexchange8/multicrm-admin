<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import { faSearch,faX,faRotateRight } from '@fortawesome/free-solid-svg-icons';
import { library } from "@fortawesome/fontawesome-svg-core";
import IbListing from "@/Pages/Member/RebateAllocation/IbListing.vue";

library.add(faSearch,faX,faRotateRight);
const page = usePage()
const user = computed(() => page.props.auth.user)

const props = defineProps({
    ibs: Object,
    filters: Object,
    defaultAccountSymbolGroup: Object,
    ibDownlines: Object,
    get_ibs_sel: Object,
});

</script>

<template>

<AuthenticatedLayout title="Rebate Allocation">
    <template #header>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                Rebate Allocation
            </h2>
        </div>
    </template>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
            <div class="flex flex-col items-center mb-4">
                <img
                    class="h-12 w-12 rounded-full"
                    :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                    alt="ProfilePic"
                >
                <h5 class="my-1 text-xl font-medium text-gray-900 dark:text-white">{{ 'Current Tech' }}</h5>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ 'IB 12345' }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center md:text-left">
                <div class="text-black dark:text-dark-eval-3">Account type:</div>
                <div class="text-black dark:text-white">{{ 'Standard' }}</div>

                <div class="text-black dark:text-dark-eval-3">Since Date </div>
                <div class="text-black dark:text-white">{{ '24-7-23' }}</div>

                <div class="text-black dark:text-dark-eval-3">Direct IB </div>
                <div class="text-black dark:text-white">{{ '20' }}</div>

                <div class="text-black dark:text-dark-eval-3">Direct Clients </div>
                <div class="text-black dark:text-white">{{ '20' }}</div>

                <div class="text-black dark:text-dark-eval-3">Total Group IB </div>
                <div class="text-black dark:text-white">{{ '20' }}</div>

                <div class="text-black dark:text-dark-eval-3">Total Group Clients </div>
                <div class="text-black dark:text-white">{{ '20' }}</div>
            </div>
        </div>
        <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
            <div class="flex flex-col text-center md:text-left">
                <h5 class="mb-6 text-xl font-medium text-gray-900 dark:text-white">My Rebate Info</h5>
                <div
                    v-for="defaultIB in defaultAccountSymbolGroup"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm my-2"
                >
                    <div class="dark:text-dark-eval-3 uppercase">
                        {{ defaultIB.symbol_group.name }} (USD) / LOT
                    </div>
                    <div class="text-center">
                        {{ defaultIB.amount }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <IbListing
        :ibs="props.ibs"
        :filters="props.filters"
        :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
        :ibDownlines="ibDownlines"
        :get_ibs_sel="get_ibs_sel"
    />

</AuthenticatedLayout>


</template>

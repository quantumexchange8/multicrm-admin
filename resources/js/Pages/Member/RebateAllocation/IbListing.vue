<script setup>
import Action from "@/Pages/Member/RebateAllocation/Action.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {router} from "@inertiajs/vue3";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";

const props = defineProps({
    ibs: Object,
    filters: Object,
    defaultAccountSymbolGroup: Object,
    ibDownlines: Object,
    get_ibs_sel: Object,
    account_type: Number,
})

let search = ref(props.filters.search);

watch(search, debounce(function  (value) {
    router.get('/member/rebate_allocation', { search: value }, {
        preserveState:true,
        preserveScroll:true,
        replace:true,
    });
    console.log(props.account_type)
}, 300));

function resetField() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');

    // Navigate to the updated URL without the search parameter
    window.location.href = url.href;
}

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}
</script>

<template>
    <div class="w-full my-6 flex justify-end gap-4">
        <div class="relative w-full md:w-2/3">
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
                <Input withIcon id="name" type="text" placeholder="Name / Email" class="block w-full" v-model="search" @keydown="handleKeyDown" />
            </InputIconWrapper>
            <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                icon="fa-solid fa-x"
                class="flex-shrink-0 w-3 h-3 cursor-pointer"
                aria-hidden="true"
                @click="clearField"
            /></button>
        </div>
        <Button class="justify-center gap-2 rounded-full" iconOnly v-slot="{ iconSizeClasses }" @click="resetField">
            <font-awesome-icon
                icon="fa-solid fa-rotate-right"
                :class="iconSizeClasses"
                aria-hidden="true"
            />
        </Button>
    </div>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr class="uppercase">
                    <th scope="col" class="px-6 py-3">
                        IB Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IB Number
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Current Upline
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="ib in ibs.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                        {{ ib.of_user.first_name }}
                    </th>
                    <th class="px-6 py-4">
                        {{ ib.of_user.ib_id }}
                    </th>
                    <th>
                        {{ ib.of_user.upline ? ib.of_user.upline.first_name : '' }}
                        <span v-if="!ib.of_user.upline" class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-500 dark:text-purple-100 uppercase">No Upline</span>
                    </th>
                    <th>
                        {{ ib.of_user.direct_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.direct_client }}
                    </th>
                    <th>
                        {{ ib.of_user.total_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.total_client }}
                    </th>
                    <th class="px-6 py-4 space-x-2 font-thin rounded-r-full">
                        <Action
                            :ib="ib"
                            :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
                            :ibDownlines="ibDownlines"
                            :get_ibs_sel="get_ibs_sel"
                        />
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

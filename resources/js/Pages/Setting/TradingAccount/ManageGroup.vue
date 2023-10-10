<script setup>
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";

const cTraderGroups = ref({data: []});
const search = ref('');
const isLoading = ref(false);

watch(
    [search],
    debounce(function ([searchValue]) {
        getResults(1, searchValue);
    }, 300)
);

const getResults = async (page = 1, search = '') => {
    isLoading.value = true;
    try {
        let url = `/setting/getTradingAccountSettings/?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        cTraderGroups.value = response.data.groups;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.Manage Groups') }}</h2>
    <hr>
    <div class="grid grid-cols-3 mt-8 gap-6">
        <div class="space-y-2">
            <Label>{{ $t('public.Filter by Adjustment Type') }}</Label>
            <Input
                class="block w-full text-sm"
                v-model="search"
            />
        </div>
    </div>

    <div v-if="isLoading" class="w-full flex justify-center mt-8">
        <Loading />
    </div>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-8" v-else>
        <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
        <tr class="uppercase">
            <th scope="col" class="px-6 py-3 w-24">
                {{ $t('public.Display') }}
            </th>
            <th scope="col" class="px-6 py-3 w-48">
                {{ $t('public.Value') }}
            </th>
            <th scope="col" class="px-6 py-3 w-48">
                {{ $t('public.Meta Group Name') }}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="cTraderGroups.data.length === 0">
            <th colspan="3" class="py-4 text-lg text-center">
                {{ $t('public.No History') }}
            </th>
        </tr>
        <tr v-for="group in cTraderGroups.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                {{ group.display }}
            </th>
            <th class="px-6 py-4">
                {{ group.value }}
            </th>
            <th class="px-6 py-4">
                {{ group.meta_group_name }}
            </th>
        </tr>
        </tbody>
    </table>

    <div class="flex justify-end mt-4">
        <TailwindPagination
            :item-classes=paginationClass
            :active-classes=paginationActiveClass
            :data="cTraderGroups"
            :limit=1
            :keepLength="true"
            @pagination-change-page="getResults"
        />
    </div>
</template>

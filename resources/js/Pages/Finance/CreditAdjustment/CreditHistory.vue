<script setup>
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputSelect from "@/Components/InputSelect.vue";
import Label from "@/Components/Label.vue";
import {ref, watch} from "vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Badge from "@/Components/Badge.vue";
import Loading from "@/Components/Loading.vue";
import debounce from "lodash/debounce.js";
import {transactionFormat} from "@/Composables/index.js";

const props = defineProps({
    account: Object
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const { formatDate, formatType } = transactionFormat();
const emit = defineEmits(['update:creditAdjustmentModal']);
const closeModal = () => {
    emit('update:creditAdjustmentModal', false);
}

const creditHistories = ref({data: []});
const type = ref('');
const date = ref('');
const isLoading = ref(false);

watch(
    [type, date],
    debounce(function ([typeValue, dateValue]) {
        getResults(1, props.account.meta_login, typeValue, dateValue);
    }, 300)
);

const getResults = async (page = 1, meta_login = props.account.meta_login, type = '', date = '') => {
    isLoading.value = true;
    try {
        let url = `/finance/getCreditHistory/${meta_login}?page=${page}`;

        if (type) {
            url += `&type=${type}`;
        }

        if (date) {
            url += `&date=${date}`;
        }

        const response = await axios.get(url);
        creditHistories.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

const getAmountClass = (history) => {
    if (history.type === 'credit_out') {
        return 'text-[#FF3F34]';
    } else if (history.type === 'credit_in') {
        return 'text-[#05C46B]';
    }
    return '';
};

const getAmountPrefix = (history) => {
    if (history.type === 'credit_out') {
        return '-';
    } else if (history.type === 'credit_in') {
        return '+';
    }
    return '';
};

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];
</script>

<template>
    <div class="grid grid-cols-3 mt-8 gap-6">
        <div class="space-y-2">
            <Label>Filter by Adjustment Type</Label>
            <InputSelect
                class="block w-full text-sm"
                v-model="type"
            >
                <option value="">All</option>
                <option value="credit_in">Add Credit</option>
                <option value="credit_out">Withdraw Credit</option>
            </InputSelect>
        </div>
        <div class="space-y-2">
            <Label>Filter By Date</Label>
            <vue-tailwind-datepicker
                :formatter="formatter"
                v-model="date"
                input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
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
                Date
            </th>
            <th scope="col" class="px-6 py-3 w-48">
                Adjustment Type
            </th>
            <th scope="col" class="px-6 py-3 w-48">
                Amount ($)
            </th>
            <th scope="col" class="px-6 py-3 w-48">
                Allotted Time
            </th>
            <th scope="col" class="px-6 py-3">
                Description
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="creditHistories.data.length === 0">
            <th colspan="5" class="py-4 text-lg text-center">
                No History
            </th>
        </tr>
        <tr v-for="history in creditHistories.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
            <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                {{ formatDate(history.created_at) }}
            </th>
            <th class="px-6 py-4">
                {{ history.comment }}
            </th>
            <th>
                <span :class="getAmountClass(history)">{{ getAmountPrefix(history) }} {{ history.amount }}</span>
            </th>
            <th class="px-6 py-4 font-thin rounded-r-full">
                {{ history.start_date }} - {{ history.expiry_date }}
            </th>
            <th class="px-6 py-4 font-thin rounded-r-full">
                {{ history.internal_description ?? '-' }}
            </th>
        </tr>
        </tbody>
    </table>

    <div class="flex justify-end mt-4">
        <TailwindPagination
            :item-classes=paginationClass
            :active-classes=paginationActiveClass
            :data="creditHistories"
            :limit=1
            :keepLength="true"
            @pagination-change-page="getResults"
        />
    </div>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {ref, watchEffect} from "vue";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import CreateAnnouncement from "@/Pages/Announcement/Partials/CreateAnnouncement.vue";
import Modal from "@/Components/Modal.vue";
import EditAnnouncement from "@/Pages/Announcement/Partials/EditAnnouncement.vue";
import AnnouncementDetails from "@/Pages/Announcement/Partials/AnnouncementDetails.vue";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {usePage} from "@inertiajs/vue3";
library.add(faSearch,faX,faRotateRight);

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const submitSearch = async () => {
    const dateRange = date.value.split(' ~ ');

    await getResults(1, dateRange, search.value);
};

const announcements = ref({data: []});
const date = ref('');
const search = ref('');
const status = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1,  dateRange, search = '', status = '') => {
    isLoading.value = true;
    try {
        let url = `/announcement/getAnnouncements?page=${page}`;

        if (dateRange) {
            if (dateRange.length === 2) {
                const formattedDates = dateRange.map(date => `date[]=${date}`).join('&');
                url += `&${formattedDates}`;
            }
        }

        if (search) {
            url += `&search=${search}`;
        }

        if (status) {
            url += `&status=${status}`;
        }

        const response = await axios.get(url);
        announcements.value = response.data.announcements;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
getResults();
const reset = () => {
    getResults();
    date.value = '';
    search.value = '';
}
const formatCustomDate = (dateString) => {
    const startDate = new Date(dateString);
    const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Kuala_Lumpur' };
    return startDate.toLocaleDateString('en-US', options);
};

const getMediaUrlByCollection = (announcement, collectionName) => {
    const media = announcement.media;
    const foundMedia = media.find((m) => m.collection_name === collectionName);
    return foundMedia ? foundMedia.original_url : 'https://static.vecteezy.com/system/resources/thumbnails/005/392/398/small_2x/important-announcement-badge-with-megaphone-icon-free-vector.jpg';
};

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        const dateRange = date.value.split(' ~ ');
        getResults(currentPage.value, dateRange, search.value);
    }
};

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});

const activeStatus = ref("");
const setActiveStatus = async (status) => {
    activeStatus.value = status;
    const dateRange = date.value.split(' ~ ');
    await getResults(currentPage.value, dateRange, search.value, activeStatus.value);
};

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];
</script>

<template>
    <AuthenticatedLayout :title="$t('public.Announcement')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Announcement') }}
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label>{{ $t('public.Filter By Date') }}</Label>
                    <vue-tailwind-datepicker
                        :formatter="formatter"
                        v-model="date"
                        input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                    />
                </div>
                <div class="space-y-2">
                    <Label>{{ $t('public.Search By Title') }}</Label>
                    <div class="relative w-full">
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
                            <Input withIcon id="name" type="text" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                        </InputIconWrapper>
                        <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                            icon="fa-solid fa-x"
                            class="flex-shrink-0 w-3 h-3 cursor-pointer"
                            aria-hidden="true"
                            @click.prevent="clearField"
                        /></button>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center py-3"
                        >
                            {{ $t('public.Search') }}
                        </Button>
                        <Button
                            variant="danger-opacity"
                            class="justify-center py-3"
                            @click.prevent="reset"
                        >
                            {{ $t('public.Reset') }}
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <div class="flex flex-col md:flex-row gap-6 md:justify-between">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button
                    type="button"
                    class="px-6 py-2 text-sm font-medium text-gray-900 border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-[#007BFF]"
                    :class="{ 'dark:bg-transparent': activeStatus !== '', 'bg-blue-500 text-white dark:bg-[#007BFF] dark:text-white': activeStatus === '' }"
                    @click="setActiveStatus('')"
                >
                    {{ $t('public.All') }}
                </button>
                <button
                    type="button"
                    class="px-6 py-2 text-sm font-medium text-gray-900 border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-[#007BFF]"
                    :class="{ 'dark:bg-transparent': activeStatus !== 'Inactive', 'bg-blue-500 text-white dark:bg-[#007BFF] dark:text-white': activeStatus === 'Inactive' }"
                    @click="setActiveStatus('Inactive')"
                >
                    {{ $t('public.Show Inactive Only') }}
                </button>
                <button
                    type="button"
                    class="px-6 py-2 text-sm font-medium text-gray-900 border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-blue-700 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-[#007BFF]"
                    :class="{ 'dark:bg-transparent': activeStatus !== 'Active', 'bg-blue-500 text-white dark:bg-[#007BFF] dark:text-white': activeStatus === 'Active' }"
                    @click="setActiveStatus('Active')"
                >
                    {{ $t('public.Show Active Only') }}
                </button>
            </div>

            <CreateAnnouncement />
        </div>

        <div v-if="isLoading" class="w-full flex justify-center my-12">
            <Loading />
        </div>
        <div v-else class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="announcement in announcements.data" class="bg-white rounded-lg shadow dark:bg-dark-eval-1 dark:border-gray-700">
                <img class="rounded-t-lg h-96 w-full object-cover" :src="getMediaUrlByCollection(announcement, 'announcement_image')" alt="" />
                <div class="p-5">
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ announcement.title }}</h5>
                    <p class="mb-3 font-normal text-xs text-gray-700 dark:text-gray-400">posted on {{ formatCustomDate(announcement.start_date) }}</p>
                    <AnnouncementDetails
                        :announcement="announcement"
                    />
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="announcements"
                :limit=1
                :keepLength="true"
                @pagination-change-page="handlePageChange"
            />
        </div>
    </AuthenticatedLayout>
</template>

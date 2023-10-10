<script setup>
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import EditAnnouncement from "@/Pages/Announcement/Partials/EditAnnouncement.vue";
import {ref, watchEffect} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";

const props = defineProps({
    announcement: Object
})

const announcementDetailModal = ref(false)
const editDetailModal = ref(false)
const deleteDetailModal = ref(false)
const openAnnouncementDetailModal = () => {
    announcementDetailModal.value = true;
}
const openEditModal = () => {
    editDetailModal.value = true;
}
const openDeleteDetailModal = () => {
    deleteDetailModal.value = true;
}

const closeModal = () => {
    announcementDetailModal.value = false
}
const closeEditModal = () => {
    editDetailModal.value = false
}
const closeDeleteModal = () => {
    deleteDetailModal.value = false
}

const form = useForm({
    id: props.announcement.id,
})

const deleteAnnouncement = () => {
    form.delete(route('announcement.delete_announcement'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            closeDeleteModal();
        },
    })
}

const getMediaUrlByCollection = (announcement, collectionName) => {
    const media = announcement.media;
    const foundMedia = media.find((m) => m.collection_name === collectionName);
    return foundMedia ? foundMedia.original_url : '';
};

</script>

<template>
    <Button
        class="w-full flex justify-center"
        @click="openAnnouncementDetailModal"
    >
        {{ $t('public.View Details') }}
    </Button>

    <Modal :show="announcementDetailModal" @close="closeModal" max-width="2xl">
        <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="px-6 py-6 lg:px-8 space-y-4 text-gray-500 dark:text-dark-eval-4">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.View Announcement Details') }}</h2>
                <hr>

                <div class="my-6">
                    <p class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ announcement.title }}</p>
                </div>
                <div class="dark:text-white" v-html="announcement.content"></div>
                <div class="mt-4">
                    <img class="rounded-lg w-full" :src="getMediaUrlByCollection(announcement, 'announcement_image')" alt="" />
                </div>

                <div class="mt-6 flex gap-4 justify-end">
                    <Button variant="danger" @click="openDeleteDetailModal">
                        {{ $t('public.Delete') }}
                    </Button>
                    <Button @click="openEditModal">{{ $t('public.Edit') }}</Button>
                </div>
            </div>
        </div>
    </Modal>

    <Modal :show="editDetailModal" @close="closeEditModal" max-width="7xl">
        <div class="p-6">
            <EditAnnouncement
                :announcement="announcement"
                @update:editDetailModal="editDetailModal = $event"
            />
        </div>
    </Modal>

    <Modal :show="deleteDetailModal" @close="closeDeleteModal" max-width="2xl">
        <div class="p-6">
            <div class="flex justify-center">
                <svg width="150" height="150" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M41.666 62.5L41.666 50" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
                    <path d="M58.334 62.5L58.334 50" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
                    <path d="M12.5 29.1641H87.5V29.1641C82.7383 29.1641 80.3575 29.1641 78.5992 30.2179C77.5544 30.8442 76.6801 31.7185 76.0539 32.7633C75 34.5216 75 36.9024 75 41.6641V68.5159C75 75.4997 75 78.9916 72.8304 81.1611C70.6608 83.3307 67.169 83.3307 60.1852 83.3307H39.8148C32.831 83.3307 29.3392 83.3307 27.1696 81.1611C25 78.9916 25 75.4997 25 68.5159V41.6641C25 36.9024 25 34.5216 23.9461 32.7633C23.3199 31.7185 22.4456 30.8442 21.4008 30.2179C19.6425 29.1641 17.2617 29.1641 12.5 29.1641V29.1641Z" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
                    <path d="M41.9513 14.0441C42.4261 13.6011 43.4723 13.2097 44.9276 12.9305C46.383 12.6513 48.1662 12.5 50.0006 12.5C51.8351 12.5 53.6183 12.6513 55.0737 12.9305C56.529 13.2097 57.5752 13.6011 58.05 14.0441" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="mt-6 text-center">
                <h1 class="mb-4 text-2xl font-bold leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl dark:text-white" style="font-family: Montserrat,sans-serif">
                    {{ $t('public.Delete Announcement?') }}
                </h1>
                <p class="dark:text-dark-eval-3">
                    {{ $t('public.The announcement will be deleted permanently!')}}
                </p>
            </div>
            <div class="mt-6 flex gap-4 justify-center">
                <Button variant="secondary" class="px-6" @click="closeDeleteModal">
                    {{ $t('public.Cancel') }}
                </Button>
                <Button class="px-6" variant="danger" @click.prevent="deleteAnnouncement">{{ $t('public.Delete') }}</Button>
            </div>
        </div>
    </Modal>

</template>

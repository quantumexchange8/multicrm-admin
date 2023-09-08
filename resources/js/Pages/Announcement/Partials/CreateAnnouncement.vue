<script setup>
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import InputSelect from "@/Components/InputSelect.vue";
import Textarea from "@/Components/Textarea.vue";
import Checkbox from "@/Components/Checkbox.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { component as CKEditor } from '@ckeditor/ckeditor5-vue'

const createAnnouncementModal = ref(false);

const editorConfig = ref({
    toolbar: [ 'undo', 'redo', '|', 'bold', 'italic', 'link', 'blockQuote' ],
})
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const openCreateModal = () => {
    createAnnouncementModal.value = true;
}

const form = useForm({
    title: '',
    content: '',
    start_date: '',
    end_date: '',
    recipient: '',
    image: null,
    popup: null,
    popup_daily: null,
});

const submit = () => {
    form.post(route('announcement.create_announcement'), {
        onSuccess: () => {
          closeModal();
          form.reset()
        },
    });
};

const closeModal = () => {
    createAnnouncementModal.value = false
}

</script>

<template>
    <Button
        variant="primary-opacity"
        class="flex justify-center"
        @click="openCreateModal"
    >
        Create New Announcement
    </Button>

    <Modal :show="createAnnouncementModal" @close="closeModal" max-width="7xl">
        <div class="p-6">
            <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Create New Announcement</h2>
            <hr>

            <form action="">
                <div class="my-6 space-y-2">
                    <Label for="title" value="Title" />
                    <Input
                        id="title"
                        type="text"
                        step="0.01"
                        class="block w-full dark:border-0 text-xs"
                        v-model="form.title"
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="start_date" value="Post Date" />
                        <vue-tailwind-datepicker
                            as-single
                            :formatter="formatter"
                            v-model="form.start_date"
                            input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        />
                        <InputError :message="form.errors.start_date" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="end_date" value="Expired Date" />
                        <vue-tailwind-datepicker
                            as-single
                            :formatter="formatter"
                            v-model="form.end_date"
                            input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        />
                        <InputError :message="form.errors.end_date" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="recipient" value="Trigger Email" />
                        <InputSelect
                            v-model="form.recipient"
                            class="block w-full text-sm dark:border-0"
                        >
                            <option value="all">Trigger All Users</option>
                            <option value="member">Trigger Only Members</option>
                            <option value="ib">Trigger Only IBs</option>
                        </InputSelect>
                        <InputError :message="form.errors.recipient" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="image">Upload Document</Label>
                        <input
                            type="file"
                            id="image"
                            accept="image/*,.pdf"
                            @input="form.image = $event.target.files[0]"
                            class="block border border-gray-400 w-full rounded-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        />
                        <InputError :message="form.errors.image" class="mt-2" />
                    </div>
                </div>
                <div class="my-6 space-y-2">
                    <Label for="content" value="Announcement Details" />
                    <CKEditor class="ck-content" :editor="ClassicEditor" v-model="form.content" :config="editorConfig"></CKEditor>
                    <InputError :message="form.errors.content" class="mt-2" />
                </div>
                <div class="flex items-center justify-between mt-4">
                    <label class="flex items-center" for="popup">
                        <Checkbox id="popup" v-model="form.popup" />
                        <span class="ml-2 text-sm dark:text-white">Popup when user login portal</span>
                    </label>
                </div>
                <InputError :message="form.errors.popup" class="mt-2" />
                <div class="flex items-center justify-between mt-4">
                    <label class="flex items-center" for="popup_daily">
                        <Checkbox id="popup_daily" v-model="form.popup_daily" />
                        <span class="ml-2 text-sm dark:text-white">Popup everyday first login</span>
                    </label>
                </div>
                <InputError :message="form.errors.popup_daily" class="mt-2" />
                <div class="mt-6 flex gap-4 justify-end">
                    <Button variant="secondary" @click.prevent="closeModal">
                        Cancel
                    </Button>
                    <Button @click="submit" :disabled="form.processing">Create</Button>
                </div>
            </form>
        </div>
    </Modal>
</template>

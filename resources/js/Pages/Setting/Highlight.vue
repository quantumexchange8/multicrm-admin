<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import axios from 'axios';

const props = defineProps({
    highlightImage: Object
})

// Import FilePond plugins
// Please note that you need to install these plugins separately

// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

// Import the plugin code
import FilePondPluginFilePoster from 'filepond-plugin-file-poster';

// Import the plugin styles
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';

// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import Label from "@/Components/Label.vue";
import {ref, watchEffect} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

const FilePond = vueFilePond(
    FilePondPluginFilePoster,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
)

const form = useForm({
    imageHighlight: ''
})

const submit = () => {
    form.post(route('setting.update_highlights'), {
        onFinish: () => {
            form.reset()
        },
    });
};

watchEffect(() => {
    if (usePage().props.toast !== null) {
        location.reload()
    }
});

const myFiles = ref([]);
const addFormImage = (image) => {
    let arr = form.imageHighlight ? form.imageHighlight.split('|') : [];
    arr.push(image);
    form.imageHighlight = arr.join('|');
}

const removeFormImage = (image) => {
    let arr = form.imageHighlight ? form.imageHighlight.split('|') : [];

    arr.remove(image);
    form.imageHighlight = arr.join('|');
}

const handleFilePondLoad = (response) => {
    addFormImage(response)
    return response;
}

const handleFilePondInit = () => {
    myFiles.value = [];

    let arr = props.highlightImage

    for(let i = 0; i < arr.length; i++) {
        myFiles.value.push({
            source: arr[i]['original_url'],
            options: {
                type: 'local',
                metadata: {
                    poster: arr[i]['original_url']
                }
            }
        })
        addFormImage(arr[i]['original_url'])
    }
}
const handleFilePondRemove = (source, load, error) => {
    removeFormImage(source);
    load();
}
const handleFilePondRevert = (uniqueId, load, error) => {
    removeFormImage(uniqueId);
    axios.post('/setting/highlight/highlight-image-revert', {
        imageHighlight: uniqueId
    });
    load();
}

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
</script>

<template>
    <AuthenticatedLayout :title="$t('public.Highlight Setting')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Highlight Setting') }}
                </h2>
            </div>
        </template>

        <form class="space-y-2">
            <Label for="image">{{ $t('public.Highlight Images') }}</Label>
            <div class="bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
                <file-pond
                    name="imageHighlight"
                    ref="pond"
                    v-bind:allow-multiple="true"
                    accepted-file-types="image/png, image/jpeg, image/jpg"
                    :imagePreviewMaxHeight="200"
                    :filePosterMaxHeight="200"
                    v-bind:server="{
                    url: '',
                    timeout: 7000,
                    process: {
                        url: '/setting/highlight/upload-highlight-image',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $page.props.csrf_token
                        },
                        withCredentials: false,
                        onload: handleFilePondLoad,
                        onerror: () => {}
                    },
                    remove: handleFilePondRemove,
                    revert: handleFilePondRevert
                }"
                    v-bind:files="myFiles"
                    v-on:init="handleFilePondInit"
                />
            </div>
            <Button @click.prevent="submit" :disabled="form.processing">Save</Button>
        </form>
    </AuthenticatedLayout>
</template>

<style>
.filepond--root {
    font-family: Comfortaa,
    sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
}

.filepond--panel-root {
    background-color: transparent;
}

.filepond--item {
    width: calc(25% - 0.5em);
}

@media (max-width: 30em) {
    .filepond--item {
        width: calc(50% - 0.5em);
    }
}
</style>

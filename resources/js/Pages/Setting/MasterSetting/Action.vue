<script setup>
import Tooltip from "@/Components/Tooltip.vue";
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import { PencilIcon } from '@heroicons/vue/outline'
import {GearIcon} from "@/Components/Icons/outline.jsx";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    setting: Object
})

const editModal = ref(false);

const openEditModal = () => {
    editModal.value = true
}

const form = useForm({
    setting_id: props.setting.id,
    value: props.setting.value,
})

const submit = () => {
    form.patch(route('setting.update_master_setting'), {
        onSuccess: () => {
            closeModal();
        },
    });
};



const closeModal = () => {
    editModal.value = false
}

</script>

<template>
    <div class="flex justify-center">
        <Tooltip :content="$t('public.Edit')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openEditModal"
            >
                <PencilIcon
                    aria-hidden="true"
                    class="w-5 h-5 absolute"
                />
                <span class="sr-only">{{ $t('public.Edit') }}</span>
            </Button>
        </Tooltip>
    </div>

    <Modal :show="editModal" @close="closeModal" max-width="2xl">
        <div class="p-6">
            <h2
                class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
            >
                {{ $t('public.Edit') }}
            </h2>
            <hr>
            <form>
                <div class="grid gap-3 mt-6">
                    <div class="space-y-2">
                        <Label for="first_name" :value="$t('public.Value')" />

                        <Input
                            id="first_name"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="first_name"
                            v-model="form.value"
                        />

                        <InputError class="mt-2" :message="form.errors.value" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Button type="button" variant="secondary" @click="closeModal">
                        {{ $t('public.Cancel') }}
                    </Button>
                    <Button
                        variant="primary"
                        class="ml-3"
                        @click="submit"
                        :disabled="form.processing"
                    >
                        {{ $t('public.Save') }}
                    </Button>
                </div>
            </form>
        </div>
    </Modal>
</template>

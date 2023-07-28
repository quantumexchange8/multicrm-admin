<script setup>
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import Button from "@/Components/Button.vue";

const passwordInput = ref(null)
const emit = defineEmits(['update:memberDetailModal']);

const props = defineProps({
    member: Object
})

const form = useForm({
    user_id: props.member.id,
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
    form.post(route('member.reset_member_password'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            closeModal()
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
        },
    })
}

const closeModal = () => {
    emit('update:memberDetailModal', false);
}
</script>

<template>
    <h2
        class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
    >
        Reset Password
    </h2>
    <hr>

    <div class="mt-6 w-full">
        <div class="space-y-2 my-4">
            <Label for="password" value="New Password" />

            <Input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>
        <div class="space-y-2 my-4">
            <Label for="password_confirmation" value="Confirm Password" />

            <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <InputError
                :message="form.errors.password_confirmation"
                class="mt-2"
            />
        </div>
        <div class="mt-6 flex gap-4 justify-end">
            <Button variant="secondary" @click="closeModal">
                Cancel
            </Button>
            <Button @click.prevent="updatePassword" :disabled="form.processing">Save</Button>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { MailIcon, LockClosedIcon, LoginIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import ToastList from "@/Components/ToastList.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

function closeDrawer() {
    const drawer = document.getElementById("drawer-bottom-example");
    drawer.style.transform = "translateY(100%)";
    drawer.setAttribute("aria-hidden", "true");
}
</script>

<template>
    <GuestLayout :title="$t('public.Log in')">
        <ValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <ToastList />

            <div class="grid gap-6 text-center">
                <div class="space-y-2">
                    <div class="flex justify-center">
                        <img src="/assets/icon/email.png" alt="email_icon"/>
                    </div>
                    <Input id="email" type="email" class="block w-full placeholder:text-center" :placeholder="$t('public.Email')" v-model="form.email" autofocus autocomplete="username" />
                </div>

                <div class="space-y-2">
                    <div class="flex justify-center">
                        <img src="/assets/icon/password.png" alt="password_icon"/>
                    </div>
                    <Input id="password" type="password" class="block w-full placeholder:text-center" :placeholder="$t('public.Password')" v-model="form.password" required autocomplete="current-password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-gray-600">{{ $t('public.Remember me') }}</span>
                    </label>
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{iconSizeClasses}">
                        <LoginIcon aria-hidden="true" :class="iconSizeClasses" />
                        <span>{{ $t('public.Log in') }}</span>
                    </Button>
                </div>

            </div>
        </form>

    </GuestLayout>
</template>

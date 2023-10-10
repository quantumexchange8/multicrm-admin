<script setup>
import Button from "@/Components/Button.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    member: Object
})
const emit = defineEmits(['update:memberDetailModal']);

const form = useForm({
    user_id: props.member.id,
})

const deleteUser = () => {
    form.delete(route('member.delete_member'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal()
        },
    })
}

const closeModal = () => {
    emit('update:memberDetailModal', false);
}
</script>

<template>
    <div class="flex justify-center">
        <svg width="150" height="150" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M41.666 62.5L41.666 50" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
            <path d="M58.334 62.5L58.334 50" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
            <path d="M12.5 29.1641H87.5V29.1641C82.7383 29.1641 80.3575 29.1641 78.5992 30.2179C77.5544 30.8442 76.6801 31.7185 76.0539 32.7633C75 34.5216 75 36.9024 75 41.6641V68.5159C75 75.4997 75 78.9916 72.8304 81.1611C70.6608 83.3307 67.169 83.3307 60.1852 83.3307H39.8148C32.831 83.3307 29.3392 83.3307 27.1696 81.1611C25 78.9916 25 75.4997 25 68.5159V41.6641C25 36.9024 25 34.5216 23.9461 32.7633C23.3199 31.7185 22.4456 30.8442 21.4008 30.2179C19.6425 29.1641 17.2617 29.1641 12.5 29.1641V29.1641Z" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
            <path d="M41.9513 14.0441C42.4261 13.6011 43.4723 13.2097 44.9276 12.9305C46.383 12.6513 48.1662 12.5 50.0006 12.5C51.8351 12.5 53.6183 12.6513 55.0737 12.9305C56.529 13.2097 57.5752 13.6011 58.05 14.0441" stroke="#FF3F34" stroke-width="7.40741" stroke-linecap="round"/>
        </svg>
    </div>
    <div class="mt-6 text-center">
        <h1 class="mb-4 text-2xl font-bold leading-none tracking-tight text-gray-900 md:text-4xl lg:text-5xl dark:text-white" style="font-family: Montserrat,sans-serif">
            {{ $t('public.Delete Member?') }}
        </h1>
        <p class="dark:text-dark-eval-3">
            {{ $t('public.The selected member will be deleted permanently!') }}
        </p>
    </div>
    <div class="mt-6 flex gap-4 justify-center">
        <Button variant="secondary" class="px-6" @click="closeModal">
            {{ $t('public.Cancel') }}
        </Button>
        <Button class="px-6" variant="danger" @click.prevent="deleteUser">{{ $t('public.Delete') }}</Button>
    </div>
</template>

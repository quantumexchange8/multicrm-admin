<script setup>

import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import InputSelect from "@/Components/InputSelect.vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import {ref, watch} from "vue";

const props = defineProps({
    member: Object,
    countries: Object,
    getMemberId: Number,
})

const member = props.member;
const emit = defineEmits(['update:memberDetailModal']);

const closeModal = () => {
    emit('update:memberDetailModal', false);
}

const isEditing = ref(false);
const frontIdentityModal = ref(false);
const backIdentityModal = ref(false);
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const form = useForm({
    user_id: props.getMemberId,
    first_name: member.first_name,
    chinese_name: member.chinese_name,
    email: member.email,
    country: member.country,
    dob: member.dob,
    phone: member.phone,
    kyc_approval: member.kyc_approval,
    kyc_approval_description: member.kyc_approval_description
})

const submit = () => {
    if (isEditing.value) {
        form.patch(route('member.member_update'), {
            onSuccess: () => {
                isEditing.value = false;
                closeModal();
            },
        });
    } else {
        isEditing.value = true;
    }
};

const selectedCountry = ref(form.country);

// Function to update the phone field with the selected country's phone code
function onchangeDropdown() {
    const selectedCountryName = selectedCountry.value;
    const country = props.countries.find((country) => country.name_en === selectedCountryName);

    if (country) {
        form.phone = `${country.phone_code}`;
        form.country = selectedCountry;
    }
}

// Watch for changes in the selectedCountry and update the form.phone accordingly
watch(selectedCountry, () => {
    onchangeDropdown();
});

const getMediaUrlByCollection = (member, collectionName) => {
    const media = member.media;
    const foundMedia = media.find((m) => m.collection_name === collectionName);
    return foundMedia ? foundMedia.original_url : 'https://img.freepik.com/free-icon/user_318-159711.jpg';
};

const getMediaNameByCollection = (member, collectionName) => {
    const media = member.media;
    const foundMedia = media.find((m) => m.collection_name === collectionName);
    return foundMedia ? foundMedia.file_name : 'N/A';
};

const hasMediaCollection = (member, collectionName) => {
    return member.media.some((m) => m.collection_name === collectionName);
};

const openFrontIdentityModal = () => {
    frontIdentityModal.value = true
}

const openBackIdentityModal = () => {
    backIdentityModal.value = true
}

const backButton = () => {
    frontIdentityModal.value = false
    backIdentityModal.value = false
}

const toggleEdit = () => {
    isEditing.value =  !isEditing.value;
}
</script>

<template>
    <h2
        class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
    >
        View Member Details
    </h2>
    <hr>

    <div class="mt-4">
        <div class="my-2">
            <img
                class="w-24 h-24 rounded-full"
                :src="getMediaUrlByCollection(member, 'profile_photo')"
                alt="ProfilePic"
            >
        </div>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Personal Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="space-y-2">
                <Label for="first_name">Full Name</Label>

                <Input
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="first_name"
                    v-model="form.first_name"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div class="space-y-2">
                <Label for="chinese_name">Chinese Name</Label>

                <Input
                    id="chinese_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="chinese_name"
                    v-model="form.chinese_name"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.chinese_name" />
            </div>
            <div class="space-y-2">
                <Label for="dob">Date of Birth</Label>

                <vue-tailwind-datepicker :formatter="formatter" as-single v-model="form.dob" input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4" :disabled="!isEditing" />
            </div>
            <div class="space-y-2">
                <Label for="email">Email</Label>

                <Input
                    id="email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    disabled
                />
            </div>
            <div class="space-y-2">
                <Label for="country">Country</Label>

                <InputSelect v-model="selectedCountry" class="block w-full text-sm" placeholder="Choose Country" :disabled="!isEditing">
                    <option v-for="country in props.countries" :value="country.name_en" :key="country.id">{{ country.name_en }}</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.country" />
            </div>
            <div class="space-y-2">
                <Label for="phone">Mobile Phone</Label>
                <Input
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="phone"
                    v-model="form.phone"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <div class="space-y-2">
                <Label for="front_identity">
                    Proof of Identity (FRONT)
                </Label>
                <Modal :show="frontIdentityModal" @close="backButton">
                    <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="backButton">
                            <svg class="h-7 w-7 text-white"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" /></svg>
                            <span class="sr-only">Back</span>
                        </button>
                        <div class="px-6 py-6 lg:px-8">
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white"> Proof of Identity (Front)</h3>
                            <div class="flex justify-center">
                                <img class="rounded" :src="getMediaUrlByCollection(member, 'front_identity')" alt="Proof of Identity (Front)">
                            </div>
                        </div>
                    </div>
                </Modal>
                <div
                    :class="[
                            'py-2 pl-4 border-2 border-gray-400 rounded-full text-sm placeholder:text-sm',
                            'focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white',
                            'dark:border-gray-600 dark:bg-dark-eval-0 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
                        ]"
                >
                    <a v-if="hasMediaCollection(member, 'front_identity')" href="javascript:void(0);" @click.prevent="openFrontIdentityModal" class="text-blue-500 hover:underline ml-2">
                        {{ getMediaNameByCollection(member, 'front_identity') }}
                    </a>
                    <span v-else>Pending KYC</span>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="back_identity">
                    Proof of Identity (BACK)
                </Label>
                <Modal :show="backIdentityModal" @close="backButton">
                    <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="backButton">
                            <svg class="h-7 w-7 text-white"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" /></svg>
                            <span class="sr-only">Back</span>
                        </button>
                        <div class="px-6 py-6 lg:px-8">
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white"> Proof of Identity (Back)</h3>
                            <div class="flex justify-center">
                                <img class="rounded" :src="getMediaUrlByCollection(member, 'back_identity')" alt="Proof of Identity (Back)">
                            </div>
                        </div>
                    </div>
                </Modal>
                <div
                    :class="[
                            'py-2 pl-4 border-2 border-gray-400 rounded-full text-sm placeholder:text-sm',
                            'focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white',
                            'dark:border-gray-600 dark:bg-dark-eval-0 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
                        ]"
                >
                    <a v-if="hasMediaCollection(member, 'back_identity')" href="javascript:void(0);" @click.prevent="openBackIdentityModal" class="text-blue-500 hover:underline ml-2">
                        {{ getMediaNameByCollection(member, 'back_identity') }}
                    </a>
                    <span v-else>Pending KYC</span>
                </div>
            </div>

            <div class="space-y-2" v-if="member.kyc_approval === 'pending'">
                <Label for="kyc_approval">KYC Approval</Label>

                <InputSelect v-model="form.kyc_approval" class="block w-full text-sm" placeholder="Choose Status" :disabled="!isEditing">
                    <option value="pending">Pending</option>
                    <option value="approve">Approve</option>
                    <option value="reject">Reject</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.kyc_approval" />
            </div>
            <div class="space-y-2" v-if="member.kyc_approval === 'pending'">
                <Label for="kyc_approval_description">KYC Approval Description</Label>
                <Input
                    id="kyc_approval_description"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="kyc_approval_description"
                    v-model="form.kyc_approval_description"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.kyc_approval_description" />
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <Button variant="secondary" v-if="isEditing" @click="toggleEdit">
            Cancel
        </Button>
        <Button
            variant="primary"
            class="ml-3"
            @click="submit"
            :disabled="form.processing"
        >
            {{ isEditing ? 'Save' : 'Edit' }}
        </Button>
    </div>
</template>

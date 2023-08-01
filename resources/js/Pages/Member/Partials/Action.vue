<script setup>
import Button from "@/Components/Button.vue";
import {ResetPasswordIcon, TrashIcon, ViewIcon} from "@/Components/Icons/outline.jsx";
import {onMounted, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import EditMemberDetail from "@/Pages/Member/Partials/EditMemberDetail.vue";
import ManageIbAccountType from "@/Pages/Member/Partials/ManageIbAccountType.vue";
import ResetPortalPassword from "@/Pages/Member/Partials/ResetPortalPassword.vue";
import DeleteMember from "@/Pages/Member/Partials/DeleteMember.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const props = defineProps({
    member: Object,
    countries: Object,
    accountTypes: Object
})

const memberDetailModal = ref(false);
const getMemberId = ref(null);
const modalComponent = ref(null);

const openMemberDetail = (memberId, componentType) => {
    memberDetailModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewProfileComponent';
    } else if (componentType === 'resetPassword') {
        modalComponent.value = 'ResetPasswordComponent';
    } else if (componentType === 'deleteMember') {
        modalComponent.value = 'DeleteMemberComponent';
    }
}

const closeModal = () => {
    memberDetailModal.value = false
    modalComponent.value = null;
}

</script>

<template>
    <div class="hidden sm:block justify-center">
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openMemberDetail(member.id, 'view')"
        >
            <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
            <span class="sr-only">View</span>
        </Button>
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="primary-opacity"
            @click="openMemberDetail(member.id, 'resetPassword')"
        >
            <ResetPasswordIcon aria-hidden="true" class="w-6 h-6 absolute" />
            <span class="sr-only">Reset</span>
        </Button>
        <Button
            class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
            variant="danger-opacity"
            @click="openMemberDetail(member.id, 'deleteMember')"
        >
            <TrashIcon aria-hidden="true" class="w-6 h-6 absolute" />
            <span class="sr-only">Delete</span>
        </Button>
    </div>
    <div class="block sm:hidden">
        <Dropdown align="right" width="48">
            <template #trigger>
                    <span class="inline-flex rounded-md">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:ring focus:ring-gray-500 focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark-eval-1 dark:bg-transparent dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <div class="flex flex-col text-left">
                                Manage
                            </div>
                            <svg
                                class="ml-2 -mr-0.5 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </span>
            </template>

            <template #content>
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                    <li>
                        <a href="#" @click="openMemberDetail(member.id, 'view')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">View Member Detail</a>
                    </li>
                    <li>
                        <a href="#" @click="openMemberDetail(member.id, 'resetPassword')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reset Member Portal Password</a>
                    </li>
                    <li>
                        <a href="#" @click="openMemberDetail(member.id, 'deleteMember')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete Member</a>
                    </li>
                </ul>
            </template>
        </Dropdown>
    </div>

    <!-- Action Modal -->
    <Modal :show="memberDetailModal" @close="closeModal">
        <div class="p-6">

            <!-- View Content -->
            <template v-if="modalComponent === 'ViewProfileComponent'">
                <EditMemberDetail
                    :member="member"
                    :countries="countries"
                    :getMemberId="member.id"
                    @update:memberDetailModal="memberDetailModal = $event"
                />
                <ManageIbAccountType
                    :accountTypes="accountTypes"
                    :member="member"
                    @update:memberDetailModal="memberDetailModal = $event"
                />
            </template>

            <!-- Reset Content -->
            <template v-else-if="modalComponent === 'ResetPasswordComponent'">
                <ResetPortalPassword
                    :member="member"
                    @update:memberDetailModal="memberDetailModal = $event"
                />
            </template>

            <!-- Delete Content -->
            <template v-else-if="modalComponent === 'DeleteMemberComponent'">
                <DeleteMember
                    :member="member"
                    @update:memberDetailModal="memberDetailModal = $event"
                />
            </template>

        </div>
    </Modal>
</template>


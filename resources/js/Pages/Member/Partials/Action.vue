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
    <div class="flex justify-center">
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
                    v-if="member.role === 'member'"
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


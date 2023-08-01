<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import DeleteMember from "@/Pages/Member/Partials/DeleteMember.vue";
import ManageIbAccountType from "@/Pages/Member/Partials/ManageIbAccountType.vue";
import EditMemberDetail from "@/Pages/Member/Partials/EditMemberDetail.vue";
import ResetPortalPassword from "@/Pages/Member/Partials/ResetPortalPassword.vue";
import Modal from "@/Components/Modal.vue";
import TransferIbUpline from "@/Pages/Ib/Partials/TransferIbUpline.vue";

const props = defineProps({
    ib: Object,
    select_ibs: Object,
    countries: Object,
})

const ibDetailModal = ref(false);
const getMemberId = ref(null);
const modalComponent = ref(null);

const openIbDetail = (memberId, componentType) => {
    ibDetailModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewProfileComponent';
    } else if (componentType === 'resetPassword') {
        modalComponent.value = 'ResetPasswordComponent';
    } else if (componentType === 'deleteMember') {
        modalComponent.value = 'DeleteMemberComponent';
    }else if (componentType === 'transferIb') {
        modalComponent.value = 'transferIbComponent';
    }
}

const closeModal = () => {
    ibDetailModal.value = false
    modalComponent.value = null;
}
</script>

<template>
    <div class="flex justify-center">
        <Dropdown align="right" width="48">
            <template #trigger>
                    <span class="inline-flex rounded-md">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:ring focus:ring-gray-500 focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark-eval-1 dark:bg-transparent dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <div class="flex flex-col text-left text-xs">
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
                <ul class="py-2 text-xs text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                    <li>
                        <a href="#" @click="openIbDetail(ib.id, 'view')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">View Member Detail</a>
                    </li>
                    <li>
                        <a href="#" @click="openIbDetail(ib.id, 'resetPassword')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reset Member Portal Password</a>
                    </li>
                    <li>
                        <a href="#" @click="openIbDetail(ib.id, 'deleteMember')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete Member</a>
                    </li>
                    <li>
                        <a href="#" @click="openIbDetail(ib.id, 'transferIb')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Transfer IB Upline</a>
                    </li>
                </ul>
            </template>
        </Dropdown>
    </div>

    <!-- Action Modal -->
    <Modal :show="ibDetailModal" @close="closeModal">
        <div class="p-6">

            <!-- View Content -->
            <template v-if="modalComponent === 'ViewProfileComponent'">
                <EditMemberDetail
                    :member="ib"
                    :countries="countries"
                    :getMemberId="ib.id"
                    @update:memberDetailModal="ibDetailModal = $event"
                />
            </template>

            <!-- Reset Content -->
            <template v-else-if="modalComponent === 'ResetPasswordComponent'">
                <ResetPortalPassword
                    :member="ib"
                    @update:memberDetailModal="ibDetailModal = $event"
                />
            </template>

            <!-- Delete Content -->
            <template v-else-if="modalComponent === 'DeleteMemberComponent'">
                <DeleteMember
                    :member="ib"
                    @update:memberDetailModal="ibDetailModal = $event"
                />
            </template>

            <!-- Transfer Upline Content -->
            <template v-else-if="modalComponent === 'transferIbComponent'">
                <TransferIbUpline
                    :ib="ib"
                    :select_ibs="props.select_ibs"
                    @update:memberDetailModal="ibDetailModal = $event"
                />
            </template>

        </div>
    </Modal>
</template>

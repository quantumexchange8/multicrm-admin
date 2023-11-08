<script setup>
import Button from "@/Components/Button.vue";
import {ResetPasswordIcon, TrashIcon, ViewIcon, IbTransferIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import EditMemberDetail from "@/Pages/Member/Partials/EditMemberDetail.vue";
import ManageIbAccountType from "@/Pages/Member/Partials/ManageIbAccountType.vue";
import ResetPortalPassword from "@/Pages/Member/Partials/ResetPortalPassword.vue";
import DeleteMember from "@/Pages/Member/Partials/DeleteMember.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {
    faArrowRightToBracket
} from "@fortawesome/free-solid-svg-icons";
import Tooltip from "@/Components/Tooltip.vue";
import TransferUpline from "@/Pages/Member/Partials/TransferUpline.vue";
library.add(faArrowRightToBracket)


const props = defineProps({
    member: Object,
    countries: Object,
    accountTypes: Object,
    getMemberSel: Array,
})

const memberDetailModal = ref(false);
const getMemberId = ref(null);
const modalComponent = ref(null);

const openMemberDetail = (memberId, componentType) => {
    memberDetailModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewProfileComponent';
    } else if (componentType === 'transferUpline') {
        modalComponent.value = 'TransferUpline';
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

const openInNewTab = (url) => {
    window.open(url, '_blank');
}


</script>

<template>
    <div class="flex justify-center">
        <Tooltip :content="$t('public.Impersonate')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="success-opacity"
                @click="openInNewTab(route('member.impersonate', member.id))"
            >
                <font-awesome-icon
                    icon="fa-solid fa-arrow-right-to-bracket"
                    class="flex-shrink-0 w-4 h-4 cursor-pointer"
                    aria-hidden="true"
                />
                <span class="sr-only">{{ $t('public.Impersonate') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.View')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openMemberDetail(member.id, 'view')"
            >
                <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">{{ $t('public.View') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.Transfer Upline')" placement="top" v-if="member.role === 'member'">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openMemberDetail(member.id, 'transferUpline')"
            >
                <IbTransferIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">{{ $t('public.Transfer Upline') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.Reset Password')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openMemberDetail(member.id, 'resetPassword')"
            >
                <ResetPasswordIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">{{ $t('public.Reset') }}</span>
            </Button>
        </Tooltip>
        <Tooltip :content="$t('public.Delete Member')" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="danger-opacity"
                @click="openMemberDetail(member.id, 'deleteMember')"
            >
                <TrashIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">{{ $t('public.Delete') }}</span>
            </Button>
        </Tooltip>
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

            <!-- Transfer Content -->
            <template v-if="modalComponent === 'TransferUpline'">
                <TransferUpline
                    :member="member"
                    :getMemberSel="props.getMemberSel"
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


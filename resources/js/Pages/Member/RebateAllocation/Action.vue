<script setup>
import Button from "@/Components/Button.vue";
import {GearIcon, IbTransferIcon, ViewIcon} from "@/Components/Icons/outline.jsx";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import ViewRebate from "@/Pages/Member/RebateAllocation/ViewRebate.vue";
import StructureRebate from "@/Pages/Member/RebateAllocation/StructureRebate.vue";
import TransferIbUpline from "@/Pages/Member/RebateAllocation/TransferIbUpline.vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps({
    ib: Object,
    defaultAccountSymbolGroup: Object,
    get_ibs_sel: Object,
})

const IbManageModal = ref(false);
const modalComponent = ref(null);

const openMemberDetail = (ibId, componentType) => {
    IbManageModal.value = true;
    if (componentType === 'view') {
        modalComponent.value = 'ViewRebate';
    } else if (componentType === 'structure') {
        modalComponent.value = 'StructureRebate';
    } else if (componentType === 'transferIb') {
        modalComponent.value = 'TransferIB';
    }
}

const closeModal = () => {
    IbManageModal.value = false
    modalComponent.value = null;
}

const handleButtonClick = () => {
    getIbDownlineRebateInfo();
    openMemberDetail(props.ib.id, 'structure');
};

const loading = ref(false);
const downlineGroupRates = ref([]);

const getIbDownlineRebateInfo = async () => {
    try {
        loading.value = true; // Show loading state

        const response = await axios.post('/member/getIbDownlineRebateInfo', {
            id: props.ib.id,
        });

        downlineGroupRates.value = response.data;
        loading.value = false; // Hide loading state after getting the response
    } catch (error) {
        console.error('Error fetching ib downline: ', error);
        loading.value = false; // Hide loading state in case of an error
    }
};

</script>

<template>
    <div class="flex justify-center">
        <Tooltip content="View" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openMemberDetail(ib.id, 'view')"
            >
                <ViewIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">View</span>
            </Button>
        </Tooltip>
        <Tooltip content="Structure" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="handleButtonClick"
            >
                <GearIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">Structure</span>
            </Button>
        </Tooltip>
        <Tooltip content="IB Transfer" placement="top">
            <Button
                class="justify-center px-4 pt-2 mx-1 rounded-full w-8 h-8 focus:outline-none"
                variant="primary-opacity"
                @click="openMemberDetail(ib.id, 'transferIb')"
            >
                <IbTransferIcon aria-hidden="true" class="w-6 h-6 absolute" />
                <span class="sr-only">Transfer</span>
            </Button>
        </Tooltip>
    </div>

    <!-- Action Modal -->
    <Modal :show="IbManageModal" @close="closeModal" max-width="7xl">
        <div class="p-6">

            <!-- View -->
            <template v-if="modalComponent === 'ViewRebate'">
                <ViewRebate
                    :ib="ib"
                    :getIbId="ib.id"
                    :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
                    @update:IbManageModal="IbManageModal = $event"
                />
            </template>

            <!-- Structure -->
            <template v-if="modalComponent === 'StructureRebate'">
                <div v-if="loading" class="w-full flex justify-center mt-4">
                    <div class="px-4 py-2 text-sm font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full animate-pulse dark:bg-blue-900 dark:text-blue-200">
                        loading...
                    </div>
                </div>
                <div v-else>
                    <StructureRebate
                        :ib="ib"
                        :getIbId="ib.id"
                        :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
                        :downlineGroupRates="downlineGroupRates"
                        @update:IbManageModal="IbManageModal = $event"
                    />
                </div>
            </template>

            <!-- Transfer IB -->
            <template v-if="modalComponent === 'TransferIB'">
                <TransferIbUpline
                    :ib="ib"
                    :get_ibs_sel="props.get_ibs_sel"
                    @update:IbManageModal="IbManageModal = $event"
                />
            </template>

        </div>
    </Modal>
</template>


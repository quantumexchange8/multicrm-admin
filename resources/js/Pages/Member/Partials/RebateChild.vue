<script setup>
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {computed, ref} from "vue";
import {useForm} from '@inertiajs/vue3';
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";
import RebateStructure from './RebateStructure.vue'
import RebateView from './RebateView.vue'

const props = defineProps({
    childrens: Object,
    ibs: Object,
    childrenAccounts: Object,
    allibs: Object,
    childdownline: Object,
    defaultAccountSymbolGroup: Object,
});


const submitAllocation = ref(false);
const showForm = ref(false);
const childDetail = ref(null);
const childdownline = props.childdownline;
const ibs = props.ibs;
const allibs = props.allibs;
const errors = ref([]);
const groupRateItems = ref(null);
const currentChildId = ref(null);
const childStructure = ref(null);
const childStructureDownline = ref(null);
const groupRateItemsChild = ref(null);
const selectedChildStructure = ref(null);

const form = useForm({
    child_id: '',

});

// if (localStorage.groupRateItems) {
//   groupRateItems.value = JSON.parse(localStorage.groupRateItems);
// }

// REBATE ALLOCATION
const openRebateAllocationModal = (IbId) => {
    const ib = props.allibs.find((ib) => ib.id === IbId);

    if(ib){
        currentChildId.value = IbId;
        childDetail.value = ib;
        submitAllocation.value = true;

    }

};


// REBATE STRUCTURE
const openRebateStructure = (selectedChildId) => {
    const selectedChild = props.allibs.find((selectedChild) => selectedChild.id === selectedChildId);

    if(selectedChild){
        currentChildId.value = selectedChildId;
        childStructure.value = selectedChild;
        submitAllocation.value = true;
        form.user_id = IbId;

    }


    selectedChildStructure.value = selectedChild;


    selectedChildStructure.value = selectedChild;

};

// const fetchDownline = (selectedChild) => {

//     const selectedChildId = selectedChild.id;

//     // Initialize the downlineUsers array with the selected child
//     let downlineUsers = [];

//     // Filter children to find those whose hierarchyList contains the selected user's ID
//     const childDownlines = props.allibs.filter((child) => {
//         const hierarchyList = child.hierarchyList;
//         // Check if the selected user's ID exists in the hierarchyList
//         return hierarchyList.includes(`-${selectedChildId}-`);
//     });

//     // Add the downline children to the downlineUsers array
//     if (childDownlines.length > 0) {
//         downlineUsers = [...downlineUsers, ...childDownlines];
//     }

//     return downlineUsers;
// }

const closeModal = () => {
    childDetail.value = null;
    selectedChildStructure.value = null;
    submitAllocation.value = false
    showForm.value = false;

    // Reset groupRateItems when the modal is closed
    groupRateItems.value = {};
    groupRateItemsChild.value = {};
    // Also remove groupRateItems from localStorage
    // delete localStorage.groupRateItems;
}

const cancel = () => {
    showForm.value = false;
    childDetail.value = null;
    submitAllocation.value = false;
    selectedChildStructure.value = null;
    // Reset groupRateItems when the user cancels
    groupRateItems.value = {};
    groupRateItemsChild.value = {};

    // Also remove groupRateItems from localStorage
    // delete localStorage.groupRateItems;
}

function formatDate(date) {
    const formattedDate = new Date(date).toISOString().slice(0, 10);
    return formattedDate.replace(/-/g, '/');
}

</script>

<template>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr class="uppercase">
                    <th scope="col" class="px-6 py-3">
                        IB Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IB Number
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Current Upline
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Direct Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group IB
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Total Group Client
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="ib in allibs" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                        {{ ib.of_user.first_name }}
                    </th>
                    <th class="px-6 py-4">
                        {{ ib.of_user.ib_id }}
                    </th>
                    <th>
                        {{ ib.of_user.upline ? ib.of_user.upline.first_name : 'No Upline' }}
                    </th>
                    <th>
                        {{ ib.of_user.direct_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.direct_client }}
                    </th>
                    <th>
                        {{ ib.of_user.total_ib }}
                    </th>
                    <th>
                        {{ ib.of_user.total_client }}
                    </th>
                    <th class="flex gap-2">
                        <button ref="buttonRef" @click="openRebateAllocationModal(ib.id)">View</button>

                        <button @click="openRebateStructure(ib.id)">structure</button>
                        <button @click="openIbTransfer">ib tt</button>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

        <!-- EDIT REBATE ALLOCATION -->
    <RebateView
        :childDetail="childDetail"
        :ibs="allibs"
        :allibs="allibs"
        :defaultAccountSymbolGroup="defaultAccountSymbolGroup"
        @cancel-edit="cancel"
        @close="closeModal"
    >

    </RebateView>

        <!-- EDIT REBATE STRUCTURE -->
            <RebateStructure
                :childStructure="childStructure"
                :childStructureDownline="childStructureDownline"
                :ibs="ibs"
                :allibs="allibs"
                :childrens="childrens"
                @cancel-rebate="cancel"
                @close="closeModal"
            >

            </RebateStructure>

</template>

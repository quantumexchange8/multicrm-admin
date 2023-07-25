<script setup>
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {computed, ref} from "vue";
import {useForm} from '@inertiajs/vue3';
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    childrens: Object,
    ibs: Object,
});


const submitAllocation = ref(false);
const showForm = ref(false);
const childDetail = ref(null);
const childrens = props.childrens;
const ibs = props.ibs;
const errors = ref([]);
const groupRateItems = ref({});
const currentChildId = ref(null);

const form = useForm({
    user_id: '',
});

if (localStorage.groupRateItems) {
  groupRateItems.value = JSON.parse(localStorage.groupRateItems);
}

const updateGroupRate = (symbolGroupId, value) => {
    groupRateItems.value[symbolGroupId] = value;

    // Save groupRateItems to localStorage
  localStorage.groupRateItems = JSON.stringify(groupRateItems.value);
};

const submitForm  = () => {
    for (const symbolGroupId in groupRateItems.value) {
        const amount = groupRateItems.value[symbolGroupId];
        const symbolGroup = ibs.symbol_groups.find((group) => group.id === Number(symbolGroupId));

        if (isNaN(amount)) {
            errors.value[symbolGroupId] = 'Please enter a valid amount';
        } else if (symbolGroup && parseFloat(amount) > parseFloat(symbolGroup.amount)) {
            const symbolGroupName = symbolGroup?.symbol_group?.name ?? '';
            errors.value[symbolGroupId] = `Invalid Amount for ${symbolGroupName}`;
        } else {
            errors.value[symbolGroupId] = '';
        }
    }

    // If there are any errors, prevent form submission
    if (Object.values(errors.value).some((error) => error !== '')) {
        return;
    }

    // Pass groupRateItems directly to the form submission
    form.post(route('updateRebate.update', { symbolGroupItems: groupRateItems.value }), {
        onSuccess: () => {
            showForm.value = false;
            childDetail.value = null;
            currentChildId.value && openRebateAllocationModal(currentChildId.value); // Return to the previous childDetail view
        },
    });
}

    const openRebateAllocationModal = (childId) => {
        const child = childrens.find((child) => child.id === childId);
        if (child) {
            currentChildId.value = childId;
            childDetail.value = child;
            submitAllocation.value = true;
            form.user_id = childId;
        }
    };

    const closeModal = () => {
        childDetail.value = null;
        submitAllocation.value = false

        // Reset groupRateItems when the modal is closed
        groupRateItems.value = {};

        // Also remove groupRateItems from localStorage
        delete localStorage.groupRateItems;
    }

    const cancel = () => {
        showForm.value = false;
        childDetail.value = null;
        submitAllocation.value = false;
        // Reset groupRateItems when the user cancels
        groupRateItems.value = {};

        // Also remove groupRateItems from localStorage
        delete localStorage.groupRateItems;
    }

    const getAmount = (childRebateInfo) => {
        const amount = groupRateItems.value[childRebateInfo.symbol_group.id] || childRebateInfo.amount;
        return parseFloat(amount).toFixed(2);
    };

    function formatDate(date) {
        const formattedDate = new Date(date).toISOString().slice(0, 10);
        return formattedDate.replace(/-/g, '/');
    }

</script>

<template>

        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            IB NAME
                        </th>
                        <th scope="col" class="px-6 py-3">
                            IB NUMBER
                        </th>
                        <th scope="col" class="px-6 py-3">
                            CURRENT UPLINE
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DIRECT IB
                        </th>
                        <th scope="col" class="px-6 py-3">
                            DIRECT CLIENTS
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL GROUP IB
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TOTAL GROUP CLIENT
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ACTIONS
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="child in childrens" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                            {{ child.of_user.first_name }}
                        </th>
                        <th class="px-6 py-4">
                            {{ child.of_user.id_no }}
                        </th>
                        <th>
                            {{ child.upline_id}}
                        </th>
                        <th>
                            {{ child.of_user.direct_ib }}
                        </th>
                        <th>
                            {{ child.of_user.direct_client }}
                        </th>
                        <th>
                            {{ child.of_user.total_ib }}
                        </th>
                        <th>
                            {{ child.of_user.total_client }}
                        </th>
                        <th class="flex gap-2">
                            <button ref="buttonRef" @click="openRebateAllocationModal(child.id)">View</button>
                            <button @click="openRebateStructure">structure</button>
                            <button @click="openIbTransfer">ib tt</button>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>

        <Modal :show="childDetail !== null" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View More Details</h2>
                <hr>
                <div class="grid grid-cols-1 md:grid-cols-2 mt-6">
                    <div>
                        <p class="text-black dark:text-white mb-5">My Rebate Info</p>
                        <div v-for="account in ibs.symbol_groups">
                            <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                                <span class="text-black dark:text-dark-eval-3 uppercase">{{ account.symbol_group.name }} (USD)/LOT</span>
                                <span class="text-black dark:text-white text-right md:text-center py-2">{{ account.amount }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-if="showForm">
                        <p class="text-black dark:text-white mb-5">{{ childDetail.of_user.first_name }}</p>

                        <form @submit.prevent="submitForm">
                            <input v-model="form.user_id" type="hidden" />
                            <div v-for="childRebateInfo in childDetail.symbol_groups" >
                                <div class="grid grid-cols-2 text-black dark:text-white md:pr-4 mb-5 items-center">
                                    <span class="text-black dark:text-dark-eval-3 uppercase">{{ childRebateInfo.symbol_group.name }} (USD)/LOT</span>
                                    <Input
                                        :id="'symbol_group_' + childRebateInfo.symbol_group.id"
                                        :name="'groupRateItems[' + childRebateInfo.symbol_group.id + ']'"
                                        :model-value="getAmount(childRebateInfo)"
                                        @input="updateGroupRate(childRebateInfo.symbol_group.id, $event.target.value)"
                                        type="number"
                                        step="0.01"
                                        min="0.00"
                                        class="block w-full"
                                    />
                                    <InputError :message="errors[childRebateInfo.symbol_group.id]" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 w-full md:w-1/2 md:float-right">
                                <Button class="px-6 justify-center" variant="danger" @click="cancel">Cancel</Button>
                                <Button class="px-6 justify-center" :disabled="form.processing">Save</Button>
                            </div>
                        </form>
                    </div>

                    <div v-else>
                        <p class="text-black dark:text-white mb-5">{{ childDetail.of_user.first_name }}</p>
                        <div v-for="childRebateInfo in childDetail.symbol_groups" >
                            <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                                <span class="text-black dark:text-dark-eval-3 uppercase">{{ childRebateInfo.symbol_group.name }} (USD)/LOT</span>
                                <span class="text-black dark:text-white text-right md:text-center py-2">{{ getAmount(childRebateInfo) }}</span>
                            </div>

                        </div>

                        <Button @click="showForm = !showForm" class="px-6 float-right">Edit</Button>
                    </div>
                </div>

            </div>
        </Modal>

</template>
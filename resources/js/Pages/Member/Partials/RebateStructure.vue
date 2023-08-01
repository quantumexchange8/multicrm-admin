<script setup>
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {computed, ref} from "vue";
import {useForm} from '@inertiajs/vue3';
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    childrens: Object,
    ibs: Object,
    childrenAccounts: Object,
    childStructure: Object,
    childStructureDownline: Object,
    childdownline: Object,
    allibs: Object,
});


const childDetail = ref(null);
const errors = ref([]);
const currentChildId = ref(null);
const groupRateItemsChild = ref({});
const ibs = props.ibs;
const childdownline = props.childdownline;

const emit = defineEmits(['cancel-rebate', 'close']);
const allibs = props.allibs;

// if (localStorage.groupRateItems) {
//   groupRateItems.value = JSON.parse(localStorage.groupRateItems);
// }

const cancel = () => {
  emit('cancel-rebate');
};

const closeModal = () => {
  emit("close");
};

const form = useForm({
    user_id: '',
    child_id: '',
    
});

const updateGroupStructure = (selectedChildId, symbolGroupIdStructure, value) => {



    if (!groupRateItemsChild.value[selectedChildId]) {
        groupRateItemsChild.value[selectedChildId] = {};
        console.log('here', selectedChildId)
    }

    groupRateItemsChild.value[selectedChildId][symbolGroupIdStructure] = value;
    // console.log('Updating groupRateItemsChild:', selectedChildId, symbolGroupIdStructure, value);

    // localStorage.groupRateItems = JSON.stringify(groupRateItemsChild.value);
};

// SUBMIT REBATE STRUCTURE
const submitFormStructure = () => {
    
    for (const symbolGroupIdStructure in groupRateItemsChild.value) {
        const amount = groupRateItemsChild.value[symbolGroupIdStructure];
        const symbolGroup = ibs.symbol_groups.find((group) => group.id === Number(symbolGroupIdStructure));

        if (isNaN(amount)) {
            errors.value[symbolGroupIdStructure] = 'Please enter a valid amount';
        } else if (symbolGroup && parseFloat(amount) > parseFloat(symbolGroup.amount)) {
            const symbolGroupName = symbolGroup?.symbol_group?.name ?? '';
            errors.value[symbolGroupIdStructure] = `Invalid Amount for ${symbolGroupName}`;
        } else {
            errors.value[symbolGroupIdStructure] = '';
        }
    }
    
    // If there are any errors, prevent form submission
    if (Object.values(errors.value).some((error) => error !== '')) {
        return;
    }

    // Update the amounts for the selected child (user 3) and its downline (user 4, and so on)
    const updateDownlineAmounts = (selectedChild) => {
        // Update the amount for the selected child
        groupRateItemsChild.value[selectedChild.id] = form[selectedChild.id];

        // Check if selectedChild.id exists in childStructureDownline
        if (childStructureDownline.value[selectedChild.id]) {
            // Convert the downline children object to an array
            const downlineChildrenArray = Object.values(childStructureDownline.value[selectedChild.id]);
            
            // Loop through the downline children array and call the function recursively
            downlineChildrenArray.forEach((downlineChild) => {
                updateDownlineAmounts(downlineChild);
            });
        } 
    };

    // Call the function to update the amounts for the selected child and its downline
    updateDownlineAmounts(childStructure.value);

    // Pass groupRateItems directly to the form submission
    form.post(route('updateRebate.update', { symbolGroupItems: groupRateItemsChild.value }), {
            onSuccess: () => {
                childStructure.value = child;
                currentChildId.value && openRebateAllocationModal(currentChildId.value); // Return to the previous childDetail view
                
            },
            
        });
        
    }

    const getStuctureAmount = (symbolAmount) => {
        const amount = groupRateItemsChild.value[symbolAmount.symbol_group.id] || symbolAmount.amount;
        console.log(amount)
        return parseFloat(amount).toFixed(2);
    };
    

    const fetchDownline = (selectedChild) => {
        
        const selectedChildId = selectedChild.id;

        // Initialize the downlineUsers array with the selected child
        let downlineUsers = [];

        // Filter children to find those whose hierarchyList contains the selected user's ID
        const childDownlines = childdownline.filter((child) => {
            const hierarchyList = child.hierarchyList;
            // Check if the selected user's ID exists in the hierarchyList
            return hierarchyList.includes(`-${selectedChildId}-`);
        });

        // Add the downline children to the downlineUsers array
        if (childDownlines.length > 0) {
            downlineUsers = [...downlineUsers, ...childDownlines];
        }
        
        return downlineUsers;
    }


</script>

<template>
<Modal :show="childStructure !== null" @close="closeModal">
    <div class="p-6">

        <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Edit Rebate Structure</h2>
        <hr>
        <div class="grid grid-cols-1 md:grid-cols-2 mt-6">
            <div>
                <p class="text-black dark:text-white mb-5">
                    IB:
                    <span>
                        
                        {{ childStructure.of_user.first_name }}
                    </span>
                </p>
            </div>
        </div>
        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr v-for="editUserStructure in childStructure.upline.symbol_groups">
                        <th scope="col" class="px-6 py-3">
                            {{ editUserStructure.symbol_group.name }} <br> (USD)/LOT
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="account in childStructure" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th>
                            {{ account.amount }}
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ALL DOWNLINE -->
        <div>
            <form @submit.prevent="submitFormStructure">
                <!-- <div v-if="Object.keys(childStructureDownline).length > 0"> -->
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                            <tr>
                                <th>
                                    IB NAME
                                </th>
                            </tr>
                            <tr v-for="account in childStructure.symbol_groups">
                                <th>
                                    {{ account.symbol_group.name }}
                                    (USD) / LOT 
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <input v-model="form.child_id" type="hidden" />
                                <tr v-for="selectedChild in childStructureDownline" :key="selectedChild.id">
                                    
                                    <th>
                                        {{ selectedChild.of_user.first_name }}
                                    </th>
                                    <th v-for="symbolAmount in selectedChild.symbol_groups" :key="symbolAmount.symbol_group.id">
                                        ID: {{ selectedChild.id }} /
                                        symID:{{ symbolAmount.symbol_group.id }}
                                        {{ symbolAmount.amount }}
                                        <Input
                                        :id="'symbol_structure_' + symbolAmount.symbol_group.id"
                                        :name="'groupRateItemsChild['+ selectedChild.id +'][' + symbolAmount.symbol_group.id + ']'"
                                        :model-value="getStuctureAmount(symbolAmount)"
                                        @input="updateGroupStructure(selectedChild.id, symbolAmount.symbol_group.id, $event.target.value)"
                                        type="number"
                                        step="0.01"
                                        min="0.00"
                                        class="block w-full"
                                        />
                                        <InputError :message="errors[symbolAmount.symbol_group.id]" />
                                    </th>
                                </tr>
                        </tbody>
                    </table>

                    <div class="grid grid-cols-2 gap-4 w-full md:w-1/2 md:float-right">
                        <Button class="px-6 justify-center" variant="danger" @click="cancel">Cancel</Button>
                        <Button class="px-6 justify-center" :disabled="form.processing">Save</Button>
                    </div>
                <!-- </div> -->
                <!-- <div v-else> -->
                    <p class="text-black dark:text-white">
                        This User Has No Downline
                    </p>
                    <div class="grid grid-cols-2 gap-4 w-full md:w-1/2 md:float-right">
                        <Button class="px-6 justify-center" variant="danger" @click="cancel">Cancel</Button>
                        <Button class="px-6 justify-center" :disabled="true">Save</Button>
                    </div>
                <!-- </div> -->
            </form>
        </div>
    </div>
</Modal>


</template>
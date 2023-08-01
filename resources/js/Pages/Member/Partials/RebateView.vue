<script setup>
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {computed, ref, watch } from "vue";
import {useForm} from '@inertiajs/vue3';
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    ibs: Object,
    childrenAccounts: Object,
    childDetail: Object,
    groupRateItems: Object,
    childdownline: Object,
    allibs: Object,
    defaultAccountSymbolGroup: Object,
    childId: Number
});

const ibs = props.ibs;
const allibs = props.allibs;
const groupRateItems = ref({});
const showForm = ref(false);
const errors = ref([]);
const emit = defineEmits(['cancel-edit', 'close']);
const childdownline = props.childdownline;

const form = useForm({
    user_id: props.childId,
});

const getAmount = (childRebateInfo) => {
    const amount = groupRateItems.value[childRebateInfo.symbol_group.id] || childRebateInfo.amount;
    return parseFloat(amount).toFixed(2);
};

const updateGroupRate = (symbolGroupId, value) => {
    groupRateItems.value[symbolGroupId] = value;
    
    // localStorage.groupRateItems = JSON.stringify(groupRateItems.value);
};



// SUBMIT REBATE ALLOCATION
const submitForm  = () => {
    for (const symbolGroupId in groupRateItems.value) {
        
        const amount = groupRateItems.value[symbolGroupId];
        
        const symbolGroup = props.childDetail[symbolGroupId];
        
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
            childDetail.value = ib;
            currentChildId.value && openRebateAllocationModal(currentChildId.value); // Return to the previous childDetail view
            
        },
    });
}

const cancel = () => {
  emit('cancel-edit');
};

const closeModal = () => {
  emit("close");
};

// if (localStorage.groupRateItems) {
//   groupRateItems.value = JSON.parse(localStorage.groupRateItems);
// }

watch(() => props.childDetail, (newValue) => {
  // Reset showForm to false when childDetail prop changes (i.e., when the modal is closed)
  if (!newValue) {
    showForm.value = false;
  }
});

</script>

<template>

<Modal :show="childDetail !== null" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View More Details</h2>
                <hr>
                <div class="grid grid-cols-1 md:grid-cols-2 mt-6">
                    <div>
                        <p class="text-black dark:text-white mb-5">Upline Rebate Info </p>
                        <div v-if="childDetail.upline_id">
                            <div v-for="uplineDetail in childDetail.upline.symbol_groups">
                                <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                                    <span class="text-black dark:text-dark-eval-3 uppercase">{{ uplineDetail.symbol_group.name }} (USD)/LOT</span>
                                    <span class="text-black dark:text-white text-right md:text-center py-2">{{ uplineDetail.amount }}</span>
                                </div>
                            </div> 
                        </div>
                        <div v-else>
                            <div v-for=" defaultIB in defaultAccountSymbolGroup">
                                <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                                    <span class="text-black dark:text-dark-eval-3 uppercase">{{ defaultIB.symbol_group.name }} (USD)/LOT</span>
                                    <span class="text-black dark:text-white text-right md:text-center py-2">{{ defaultIB.amount }}</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div v-if="showForm">
                        <p class="text-black dark:text-white mb-5">IB Name: {{ childDetail.of_user.first_name }}</p>

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
                        <p class="text-black dark:text-white mb-5">IB Name: {{ childDetail.of_user.first_name }}</p>
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
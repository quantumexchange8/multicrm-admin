<script setup>
import {onMounted, ref, watchEffect} from "vue";
import {useForm} from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    ib: Object,
    getIbId: Number,
    defaultAccountSymbolGroup: Object,
    downlineGroupRates: Object
})
const emit = defineEmits(['update:IbManageModal']);
const ibGroupRates = {};
props.downlineGroupRates.forEach((ibDownline) => {
    const ibDownlineId = ibDownline.id;
    ibGroupRates[ibDownlineId] = {};
    ibDownline.symbol_groups.forEach((ibRebateInfo) => {
        ibGroupRates[ibDownlineId][ibRebateInfo.symbol_group.id] = ibRebateInfo.amount;
    });
});

const form = useForm({
    ibGroupRates
});

const submitForm = () => {
    form.post(route('member.updateRebateStructure'), {
        onSuccess: () => {
            closeModal();
        },
    });
}

const closeModal = () => {
    emit('update:IbManageModal', false);
}
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Edit Rebate Structure</h2>
    <hr>

    <h3 class="text-lg mb-2 font-medium text-gray-900 dark:text-dark-eval-4 mt-6">IB: {{ ib.of_user.first_name }}</h3>

    <div class="relative overflow-x-auto sm:rounded-lg my-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
            <tr class="uppercase">
                <th scope="col" class="px-6 py-3" v-for="uplineSymbolGroup in ib.symbol_groups">
                    {{ uplineSymbolGroup.symbol_group.name }} <br>
                    (USD) / LOT
                </th>
            </tr>
            </thead>
            <tbody>
                <tr class="bg-white dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <td class="px-6 py-4 font-thin" v-for="uplineRebateInfo in ib.symbol_groups" :key="uplineRebateInfo.id">
                        {{ uplineRebateInfo.amount }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="h-0.5 bg-gray-200 border-0 dark:bg-dark-eval-2">

    <div class="relative overflow-x-auto sm:rounded-lg my-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
            <tr class="uppercase">
                <th scope="col" class="px-6 py-3 uppercase">
                    Ib Name
                </th>
                <th scope="col" class="px-2 py-3" v-for="uplineSymbolGroup in ib.symbol_groups">
                    {{ uplineSymbolGroup.symbol_group.name }} <br>
                    (USD) / LOT
                </th>
            </tr>
            </thead>
            <tbody>
                <template v-if="downlineGroupRates.length === 0">
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <span class="bg-indigo-100 text-indigo-800 text-md font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">No Downline Found</span>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="ibDownline in downlineGroupRates" :key="ibDownline.id" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <td>{{ ibDownline.of_user.first_name }}</td>
                        <td class="px-2 py-4 font-thin" v-for="ibRebateInfo in ibDownline.symbol_groups" :key="ibRebateInfo.id">
                            <Input
                                :id="`group_${ibRebateInfo.id}`"
                                :model-value="ibRebateInfo.amount"
                                v-model="form.ibGroupRates[ibDownline.id][ibRebateInfo.symbol_group.id]"
                                type="number"
                                step="0.01" min="0.00"
                                class="w-28 text-xs"
                            />

                            <InputError :message="form.errors[`ibGroupRates.${ibDownline.id}.${ibRebateInfo.symbol_group.id}`]" class="mt-2" />

                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="mt-6 flex justify-end gap-2">
        <Button variant="secondary" @click="closeModal">
            Cancel
        </Button>
        <Button @click.prevent="submitForm">
            Save
        </Button>
    </div>
    </div>
</template>

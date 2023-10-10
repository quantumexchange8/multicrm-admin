<script setup>
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import {useForm} from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";

const props = defineProps({
    ib: Object,
    getIbId: Number,
    defaultAccountSymbolGroup: Object
});

const emit = defineEmits(['update:IbManageModal']);
const closeModal = () => {
    emit('update:IbManageModal', false);
}

const showForm = ref(false);

const ibGroupRates = {};
props.ib.symbol_groups.forEach((ibRebateInfo) => {
    ibGroupRates[ibRebateInfo.symbol_group.id] = ibRebateInfo.amount;
});

const form = useForm({
    user_id: props.getIbId,
    upline_id: props.ib.upline_id,
    ibGroupRates
});

const submitForm = () => {
    form.post(route('member.updateRebate'), {
        onSuccess: () => {
            showForm.value = false;
            closeModal();
        },
    });
}

const cancel = () => {
    showForm.value = false
}

</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.View More Details') }}</h2>
    <hr>

    <div class="grid grid-cols-1 md:grid-cols-2 mt-6">
        <div>
            <p class="text-black dark:text-white mb-5">{{ $t('public.Upline Rebate Info') }}</p>
            <div v-if="ib.upline">
                <div v-for="uplineRebate in ib.upline.symbol_groups">
                    <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                        <span class="text-black dark:text-dark-eval-3 uppercase">{{ uplineRebate.symbol_group.name }} (USD)/LOT</span>
                        <span class="text-black dark:text-white text-right md:text-center py-2">{{ uplineRebate.amount }}</span>
                    </div>
                </div>
            </div>
            <div v-else>
                <div v-for="accountSymbolGroup in defaultAccountSymbolGroup">
                    <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                        <span class="text-black dark:text-dark-eval-3 uppercase">{{ accountSymbolGroup.symbol_group.name }} (USD)/LOT</span>
                        <span class="text-black dark:text-white text-right md:text-center py-2">{{ accountSymbolGroup.amount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showForm">
            <p class="text-black dark:text-white mb-5">{{ $t('public.IB Name') }} : {{ ib.of_user.first_name }}</p>

            <form @submit.prevent="submitForm">
                <div v-for="ibRebateInfo in ib.symbol_groups" :key="ibRebateInfo.id">
                    <div class="grid grid-cols-2 text-black dark:text-white md:pr-4 mb-5 items-center">
                        <span class="text-black dark:text-dark-eval-3 uppercase">{{ ibRebateInfo.symbol_group.name }} (USD)/LOT</span>
                        <Input
                            :id="`group_${ibRebateInfo.id}`"
                            :model-value="ibRebateInfo.amount"
                            v-model="form.ibGroupRates[ibRebateInfo.symbol_group.id]"
                            type="number"
                            step="0.01" min="0.00"
                        />
                    </div>

                    <InputError :message="form.errors[`ibGroupRates.${ibRebateInfo.symbol_group.id}`]" class="mb-4" />

                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-1/2 md:float-right">
                    <Button variant="secondary" class="justify-center" @click="cancel">
                        {{ $t('public.Cancel') }}
                    </Button>
                    <Button class="px-6 justify-center" :disabled="form.processing">{{ $t('public.Save') }}</Button>
                </div>
            </form>
        </div>

        <div v-else>
            <p class="text-black dark:text-white mb-5">{{ $t('public.IB Name') }} : {{ ib.of_user.first_name }}</p>
            <div v-for="ibRebateInfo in ib.symbol_groups" :key="ibRebateInfo.id">
                <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                    <span class="text-black dark:text-dark-eval-3 uppercase">{{ ibRebateInfo.symbol_group.name }} (USD)/LOT</span>
                    <span class="text-black dark:text-white text-right md:text-center py-2">{{ ibRebateInfo.amount }}</span>
                </div>

            </div>

            <Button @click="showForm = !showForm" class="px-6 float-right">{{ $t('public.Edit') }}</Button>
        </div>
    </div>
</template>

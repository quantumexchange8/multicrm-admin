<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import InputSelect from "@/Components/InputSelect.vue";

const props = defineProps({
    leverages: Object,
    groups: Object,
})

const accountTypeModal = ref(false);

const openCreateModal = () => {
    accountTypeModal.value = true;
}

const form = useForm({
    name: '',
    group: '',
    minimum_deposit: '',
    leverage: '',
    currency: '',
    allow_create_account: -1,
    commission_structure: '',
    trade_open_duration: '',
});

const submit = () => {
    form.post(route('platform_configuration.addAccountType'), {
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    });
};

const currencies = [
    { value: 'USD', name: 'USD' },
    { value: 'GBP', name: 'GBP' },
    { value: 'EUR', name: 'EUR' },
    { value: 'USDT', name: 'USDT' },
    { value: 'ETH', name: 'ETH' },
    { value: 'BITCOIN', name: 'BITCOIN' },
]

const durations = [
    { value: '1', name: '1 sec' },
    { value: '2', name: '2 sec' },
    { value: '3', name: '3 sec' },
    { value: '4', name: '4 sec' },
    { value: '5', name: '5 sec' },
    { value: '6', name: '6 sec' },
    { value: '7', name: '7 sec' },
    { value: '8', name: '8 sec' },
    { value: '9', name: '9 sec' },
    { value: '10', name: '10 sec' },
    { value: '60', name: '1 min' },
    { value: '120', name: '2 min' },
    { value: '180', name: '3 min' },
    { value: '240', name: '4 min' },
    { value: '300', name: '5 min' },
]

const closeModal = () => {
    accountTypeModal.value = false
}
</script>

<template>
    <Button
        variant="primary-opacity"
        class="px-6"
        @click="openCreateModal"
    >
        Create New Account Type
    </Button>

    <Modal :show="accountTypeModal" @close="closeModal" max-width="7xl">
        <div class="p-6">
            <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Create New Account Type</h2>
            <hr>

            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="space-y-2">
                        <Label for="name">Account Type</Label>
                        <Input
                            id="name"
                            type="text"
                            step="0.01"
                            class="block w-full dark:border-0 text-xs"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="group">Group Name</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="group"
                            v-model="form.group"
                            placeholder="Select a group"
                        >
                            <option v-for="(groupName, key ) in groups" :value="key">{{ groupName }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.group" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="minimum_deposit">Minimum Deposit</Label>
                        <Input
                            id="minimum_deposit"
                            placeholder="0.00"
                            type="number"
                            step="0.01"
                            class="block w-full dark:border-0 text-xs"
                            v-model="form.minimum_deposit"
                        />
                        <InputError :message="form.errors.minimum_deposit" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="leverage">Leverage</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="leverage"
                            v-model="form.leverage"
                            placeholder="Select a leverage"
                        >
                            <option value="free">Free</option>
                            <option v-for="(leverage, key) in props.leverages" :value="key">{{ leverage }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.leverage" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="currency">Currency</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="currency"
                            v-model="form.currency"
                            placeholder="Select a currency"
                        >
                            <option v-for="currency in currencies" :value="currency.value">{{ currency.name }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.currency" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="allow_create_account">Allow New Account Creation</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="allow_create_account"
                            v-model="form.allow_create_account"
                        >
                            <option value=1>Yes</option>
                            <option value=-1>No</option>
                        </InputSelect>
                        <InputError :message="form.errors.allow_create_account" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="commission_structure">Commission Structure</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="commission_structure"
                            v-model="form.commission_structure"
                            placeholder="Select a commission structure"
                        >
                            <option value="self">Self</option>
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percent</option>
                        </InputSelect>
                        <InputError :message="form.errors.commission_structure" class="mt-2" />
                    </div>
                    <div class="space-y-2">
                        <Label for="trade_open_duration">Trade Open Duration</Label>
                        <InputSelect
                            class="block w-full dark:border-0 text-xs"
                            id="trade_open_duration"
                            v-model="form.trade_open_duration"
                            placeholder="Select a trade open duration"
                        >
                            <option v-for="duration in durations" :value="duration.value">{{ duration.name }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.trade_open_duration" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <Button variant="secondary" @click.prevent="closeModal">
                        Cancel
                    </Button>
                    <Button
                        variant="primary"
                        class="ml-3"
                        @click.prevent="submit"
                        :disabled="form.processing"
                    >
                        Create
                    </Button>
                </div>
            </form>

        </div>
    </Modal>
</template>

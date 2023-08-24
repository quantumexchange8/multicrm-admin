<script setup>
import {ref} from "vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import InputSelect from "@/Components/InputSelect.vue";
import Button from "@/Components/Button.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    account: Object,
    countries: Object,
    bankProof: Object,
    isEditing: Boolean,
})
const bankProofModal = ref(false);
const emit = defineEmits(['update:isEditing', 'update:paymentAccountModal']);
const openBankProofModal = () => {
    bankProofModal.value = true
}

const form = useForm({
    payment_account_id: props.account.id,
    name: props.account.user.first_name,
    email: props.account.user.email,
    payment_platform: props.account.payment_platform,
    payment_platform_name: props.account.payment_platform_name,
    bank_branch_address: props.account.bank_branch_address,
    payment_account_name: props.account.payment_account_name,
    account_no: props.account.account_no,
    bank_swift_code: props.account.bank_swift_code,
    bank_code_type: props.account.bank_code_type,
    bank_code: props.account.bank_code,
    country: props.account.country,
    currency: props.account.currency,
})

const submit = () => {
    if (props.isEditing) {
        form.patch(route('finance.update_payment_account'), {
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        emit('update:isEditing', true);
    }
};

const closeModal = () => {
    emit('update:paymentAccountModal', false);
    emit('update:isEditing', false);
}

const closeProofModal = () => {
    bankProofModal.value = false
}

const toggleEdit = () => {
    if (props.isEditing) {
        closeModal();
    } else {
        emit('update:isEditing', true);
    }
}
</script>

<template>
    <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View Bank Account Details</h2>
    <hr>

    <form class="my-6">
        <div class="grid col-span-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <Label for="name">Name</Label>

                <Input
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    autocomplete="bank_name"
                    disabled
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="space-y-2">
                <Label for="email">Email</Label>

                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autocomplete="bank_name"
                    disabled
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="space-y-2">
                <Label for="payment_platform_name" value="Bank Name" />

                <Input
                    id="payment_platform_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.payment_platform_name"
                    autocomplete="bank_name"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.payment_platform_name" />
            </div>
            <div class="space-y-2">
                <Label for="bank_branch_address" value="Bank Branch Address" />

                <Input
                    id="bank_branch_address"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_branch_address"
                    autocomplete="bank_branch_address"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.bank_branch_address" />
            </div>
            <div class="space-y-2">
                <Label for="payment_account_name" value="Bank Account Holder Name" />

                <Input
                    id="payment_account_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.payment_account_name"
                    autocomplete="payment_account_name"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.payment_account_name" />
            </div>
            <div class="space-y-2">
                <Label for="account_no" value="Account No." />

                <Input
                    id="account_no"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.account_no"
                    autocomplete="account_no"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.account_no" />
            </div>
            <div class="space-y-2">
                <Label for="bank_swift_code" value="Bank Swift Code" />

                <Input
                    id="bank_swift_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_swift_code"
                    autocomplete="bank_swift_code"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.bank_swift_code" />
            </div>
            <div class="space-y-2">
                <Label for="bank_code">Bank Code</Label>

                <Input
                    id="bank_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_code"
                    autocomplete="bank_code"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.bank_code" />
            </div>
            <div class="space-y-2">
                <Label for="bank_code_type">ABA / IBAN</Label>

                <Input
                    id="bank_code_type"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_code_type"
                    autocomplete="bank_code_type"
                    :disabled="!isEditing"
                />

                <InputError class="mt-2" :message="form.errors.bank_code_type" />
            </div>
            <div class="space-y-2">
                <Label for="country" value="Country" />

                <InputSelect v-model="form.country" class="block w-full text-sm" :disabled="!isEditing" placeholder="Select a country">
                    <option v-for="country in props.countries" :value="country.name_en" :key="country.id">{{ country.name_en }}</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.country" />
            </div>
            <div class="space-y-2">
                <Label for="currency" value="Your Country Currency" />

                <InputSelect v-model="form.currency" class="block w-full text-sm" :disabled="!isEditing" placeholder="Select a currency">
                    <option value="VND">VND</option>
                    <option value="MYR">MYR</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.currency" />
            </div>
            <div class="space-y-2">
                <Label for="proof_of_bank">Proof of Bank Account</Label>

                <Input
                    v-if="bankProof"
                    id="front_identity"
                    type="text"
                    class="mt-1 block w-full cursor-pointer text-blue-500 dark:text-blue-500 hover:underline bg-white dark:bg-dark-eval-0"
                    value="Click to view"
                    @click="openBankProofModal"
                    readonly
                />
                <Input
                    v-else
                    id="front_identity"
                    type="text"
                    class="mt-1 block w-full"
                    value="Proof of Bank Account"
                    disabled
                />

                <Modal :show="bankProofModal" @close="closeProofModal">
                    <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                        <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeProofModal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="px-6 py-6 lg:px-8">
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Proof of Bank Account</h3>
                            <div class="flex justify-center" v-for="bankFile in bankProof">
                                <img class="rounded" :src="bankFile.original_url" alt="Proof of Identity (Front)">
                            </div>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <Button variant="secondary" v-if="isEditing" @click.prevent="toggleEdit">
                Cancel
            </Button>
            <Button
                variant="primary"
                class="ml-3"
                @click.prevent="submit"
                :disabled="form.processing"
            >
                {{ props.isEditing ? 'Save' : 'Edit' }}
            </Button>
        </div>
    </form>
</template>

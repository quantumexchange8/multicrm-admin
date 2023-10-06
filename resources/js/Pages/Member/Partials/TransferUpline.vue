<script setup>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputError from "@/Components/InputError.vue";
import Multiselect from "@vueform/multiselect";
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

const props = defineProps({
    member: Object,
    getMemberSel: Array,
})

const emit = defineEmits(['update:memberDetailModal']);

const loading = ref(false);
const selectedEmail = ref('');

const form = useForm({
    id: props.member.id,
    new_upline: '',
})

const handleEmailSelection = (selectedValue) => {
    form.new_upline = selectedValue;
};

const submit = () => {
    form.post(route('member.transfer_upline'), {
        preserveScroll: true,
        onSuccess: () => {
            // form.reset()
            closeModal()
        },
    })
}

const closeModal = () => {
    emit('update:memberDetailModal', false);
}
</script>

<template>
    <h2
        class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
    >
        Transfer Upline
    </h2>
    <hr>

    <div class="mt-6 w-full grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3 my-2">
            <Label for="current_ib" class="text-xs" value="Current Upline" />

            <Input
                id="current_ib"
                :model-value="props.member.upline ? props.member.upline.email : 'No Upline'"
                type="email"
                class="mt- block w-full"
                readonly
            />
        </div>

        <div class="space-y-3 my-2">
            <Label for="new_upline" class="text-xs" value="New Upline" />
            <Multiselect
                v-model="selectedEmail"
                @update:modelValue="handleEmailSelection"
                placeholder="Search IB"
                :options="getMemberSel"
                :searchable="true"
                :classes="{
                    container: 'relative rounded-full mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-gray-400 rounded bg-white text-sm leading-snug outline-none dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
                    containerDisabled: 'cursor-default bg-gray-100',
                    containerActive: 'ring ring-blue-500 ring-offset-white',
                    singleLabel: 'flex items-center h-full max-w-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 pr-16 box-border rtl:left-auto rtl:right-0 rtl:pl-0 rtl:pr-3.5',
                    singleLabelText: 'overflow-ellipsis overflow-hidden block whitespace-nowrap max-w-full',
                    multipleLabel: 'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 rtl:left-auto rtl:right-0 rtl:pl-0 rtl:pr-3.5',
                    search: 'w-full absolute inset-0 outline-none focus:ring-0 appearance-none box-border border-0 text-sm font-sans bg-white rounded-full pl-3.5 rtl:pl-0 rtl:pr-3.5 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
                    placeholder: 'flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-gray-400 rtl:left-auto rtl:right-0 rtl:pl-0 rtl:pr-3.5',
                    caret: 'bg-multiselect-caret bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-none rtl:mr-0 rtl:ml-3.5',
                    caretOpen: 'rotate-180 pointer-events-auto',
                    clear: 'pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80 rtl:pr-0 rtl:pl-3.5',
                    clearIcon: 'bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block',
                    dropdown: 'max-h-40 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-white flex flex-col rounded dark:bg-[#202020] dark:text-gray-300',
                    dropdownTop: '-translate-y-full top-px bottom-auto rounded-b-none rounded-t',
                    dropdownHidden: 'hidden',
                    options: 'flex flex-col p-0 m-0 list-none',
                    optionsTop: '',
                    option: 'flex items-center justify-start box-border text-left cursor-pointer text-sm leading-snug py-2 px-3',
                    optionPointed: 'text-gray-800 bg-gray-100',
                    optionSelected: 'text-white bg-blue-500',
                    optionDisabled: 'text-gray-300 cursor-not-allowed',
                    optionSelectedPointed: 'text-white bg-blue-500 opacity-90',
                    optionSelectedDisabled: 'text-gray-400 bg-blue-500 bg-opacity-50 cursor-not-allowed',
                    noOptions: 'py-2 px-3 text-gray-600 bg-transparent text-left dark:text-white',
                    noResults: 'py-2 px-3 text-gray-600 bg-transparent text-left dark:text-white',
                    fakeInput: 'bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent',
                    spacer: 'h-9 py-px box-content',
                }"
            />

            <InputError
                :message="form.errors.new_upline"
                class="mt-2"
            />
        </div>
    </div>
    <div class="mt-6 flex gap-4 justify-end">
        <Button type="button" variant="secondary" @click="closeModal">
            Cancel
        </Button>
        <Button @click.prevent="submit" :disabled="form.processing">Save</Button>
    </div>
</template>

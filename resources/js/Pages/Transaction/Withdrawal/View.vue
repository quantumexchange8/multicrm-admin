<script setup>
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {computed, ref} from "vue";
import Textarea from "@/Components/Textarea.vue";
import {faCopy} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import toast from "@/Composables/toast.js";
library.add(faCopy);

const props = defineProps({
    withdrawal: Object,
    type: String
})
const { formatAmount } = transactionFormat();

const withdrawalLabel = computed(() => {
    if (props.withdrawal.channel === 'bank') {
        return 'Withdraw to Bank Account';
    } else if (props.withdrawal.channel === 'crypto') {
        return 'Withdraw to Cryptocurrency Account';
    }
});

const textToCopy = props.withdrawal.account_no;
const inputToCopy = ref(null);
function copyAddress() {
    if (inputToCopy.value) {
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        toast.add({
            message: "Copied",
        });
    }
}
</script>

<template>
    <div v-if="type !== 'history'">
        <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View More Details</h2>
        <hr>
        <div class="flex justify-center flex-col text-center mt-8 space-y-2">
            <h4 class="text-lg font-medium text-gray-900 dark:text-dark-eval-4">Cash Wallet Balance</h4>
            <h3 class="text-4xl mb-2 font-medium text-gray-900 dark:text-gray-100">$ {{ formatAmount(withdrawal.of_user.cash_wallet) }}</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 md:gap-4 mt-4">
            <div class="space-y-2 mb-4 md:mb-0">
                <Label>{{ withdrawal.channel === 'bank' ? 'Bank Account' : 'Crypto Network' }}</Label>
                <Input
                    class="block w-full dark:border-0 text-xs"
                    disabled
                    :model-value="withdrawal.account_type"
                />
            </div>
            <div class="space-y-2 col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label>{{ withdrawalLabel }}</Label>
                        <div class="relative w-full">
                            <Input
                                class="block w-full dark:border-0 text-xs"
                                disabled
                                :value="textToCopy"
                                ref="inputToCopy"
                            />
                            <button type="submit" class="absolute right-1 bottom-1 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm">
                                <font-awesome-icon
                                    icon="fa-solid fa-copy"
                                    class="flex-shrink-0 w-4 h-4 cursor-pointer"
                                    aria-hidden="true"
                                    @click.stop.prevent="copyAddress"
                                />
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label>Withdrawal Amount</Label>
                        <Input
                            class="block w-full dark:border-0 text-xs"
                            disabled
                            :model-value="'$ ' + formatAmount(withdrawal.amount)"
                        />
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div v-else>
        <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View Reason</h2>
        <hr>

        <Label for="comment" class="mt-8" value="Remarks"></Label>
        <Textarea
            class="mt-2"
            id="comment"
            :model-value="withdrawal.description"
            disabled
        />
    </div>

</template>

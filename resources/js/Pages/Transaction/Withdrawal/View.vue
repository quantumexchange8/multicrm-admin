<script setup>
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {computed} from "vue";
import Textarea from "@/Components/Textarea.vue";

const props = defineProps({
    withdrawal: Object,
    type: String
})

const withdrawalLabel = computed(() => {
    if (props.withdrawal.channel === 'bank') {
        return 'Withdraw to Bank Account';
    } else if (props.withdrawal.channel === 'crypto') {
        return 'Withdraw to Cryptocurrency Account';
    }
});

function formatAmount(amount) {
    return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
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

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="space-y-2">
                <Label>{{ withdrawalLabel }}</Label>
                <Input
                    class="block w-full dark:border-0"
                    disabled
                    :model-value="withdrawal.account_type + ' - ' + withdrawal.account_no"
                />
            </div>
            <div class="space-y-2">
                <Label>Withdrawal Amount</Label>
                <Input
                    class="block w-full dark:border-0"
                    disabled
                    :model-value="'$ ' + formatAmount(withdrawal.amount)"
                />
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

<script setup>
import RightbarTitle from "@/Components/Rightbar/RightbarTitle.vue";
import RightbarContent from "@/Components/Rightbar/RightbarContent.vue";
import {usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import Button from "@/Components/Button.vue";
// import { usePermission } from '@/Composables/permissions.js'

const page = usePage();
const totalApprovedDeposit = page.props.totalApprovedDeposit;
const totalPendingDeposit = page.props.totalPendingDeposit;
const totalApprovedWithdrawal = page.props.totalApprovedWithdrawal;
const totalPendingWithdrawal = page.props.totalPendingWithdrawal;
const monthlyWithdrawal = page.props.monthlyWithdrawal;
const IBAccountTypes = page.props.IBAccountTypes;
const user = computed(() => page.props.auth.user)
// const { hasRole } = usePermission();

function formatAmount(amount) {
    return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}


</script>

<template>
    <aside class="w-full md:w-auto space-y-4">
        <RightbarTitle title="Today's Deposit">
            <RightbarContent title="Total Approved Deposit ($)" :amount="formatAmount(totalApprovedDeposit)" />
            <RightbarContent title="Total Pending Deposit ($)" :amount="formatAmount(totalPendingDeposit)" />
        </RightbarTitle>

        <RightbarTitle title="Today's Withdrawal">
            <RightbarContent title="Total Approved Withdrawal ($)" :amount="formatAmount(totalApprovedWithdrawal)" />
            <RightbarContent title="Total Pending Withdrawal ($)" :amount="formatAmount(totalPendingWithdrawal)" />
        </RightbarTitle>

        <RightbarTitle title="Today's Rebate">
            <RightbarContent title="Total Approved Rebate ($)" :amount="formatAmount(totalApprovedWithdrawal)" />
            <RightbarContent title="Total Pending Rebate ($)" :amount="formatAmount(totalPendingWithdrawal)" />
        </RightbarTitle>

<!--        <RightbarTitle title="Today's Credit">-->
<!--            <RightbarContent title="Total Approved Withdrawal ($)" :amount="formatAmount(totalApprovedWithdrawal)" />-->
<!--            <RightbarContent title="Total Pending Withdrawal ($)" :amount="formatAmount(totalPendingWithdrawal)" />-->
<!--        </RightbarTitle>-->
    </aside>
</template>

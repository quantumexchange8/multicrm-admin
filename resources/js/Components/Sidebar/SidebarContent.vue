<script setup>
import PerfectScrollbar from '@/Components/PerfectScrollbar.vue'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { TemplateIcon } from '@heroicons/vue/outline'
import { library } from '@fortawesome/fontawesome-svg-core'
/* import specific icons */
import {
    faBullhorn,
    faCoins,
    faGaugeHigh, faGear,
    faLaptopFile,
    faMoneyBillTransfer,
    faUser,
    faUserTie
} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
library.add(faGaugeHigh, faUser, faUserTie, faMoneyBillTransfer, faCoins, faLaptopFile, faGear, faBullhorn)

</script>

<template>
    <PerfectScrollbar
        tagname="nav"
        aria-label="main"
        class="relative flex flex-col flex-1 max-h-full gap-4 px-3"
    >
        <SidebarLink
            :title="$t('public.sidebar.Dashboard')"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-gauge-high"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <!-- announcement -->
        <SidebarLink
            :title="$t('public.sidebar.Announcement')"
            :href="route('announcement.index')"
            :active="route().current('announcement.index')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-bullhorn"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <!-- member -->
        <SidebarCollapsible
            :title="$t('public.sidebar.Members')"
            :active="route().current('member.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-user"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('member.member_listing')"
                :title="$t('public.sidebar.Member Listing')"
                :active="route().current('member.member_listing')"
            />

            <SidebarCollapsibleItem
                :href="route('member.member_tree')"
                :title="$t('public.sidebar.Member Tree')"
                :active="route().current('member.member_tree')"
            />

            <SidebarCollapsibleItem
                :href="route('member.rebate_payout')"
                :title="$t('public.sidebar.Rebate Payout')"
                :active="route().current('member.rebate_payout')"
            />

            <SidebarCollapsibleItem
                :href="route('member.rebate_allocation')"
                :title="$t('public.sidebar.Rebate Allocation')"
                :active="route().current('member.rebate_allocation')"
            />
        </SidebarCollapsible>

        <!-- transaction -->
        <SidebarCollapsible
            :title="$t('public.sidebar.Finance')"
            :active="route().current('finance.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-coins"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('finance.payment_account_listing')"
                :title="$t('public.sidebar.Payment Account Listing')"
                :active="route().current('finance.payment_account_listing')"
            />
            <SidebarCollapsibleItem
                :href="route('finance.credit_amount_adjustment')"
                :title="$t('public.sidebar.Credit Amount Adjustment')"
                :active="route().current('finance.credit_amount_adjustment')"
            />
        </SidebarCollapsible>

        <!-- transaction -->
        <SidebarCollapsible
            :title="$t('public.sidebar.Transaction')"
            :active="route().current('transaction.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-money-bill-transfer"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('transaction.deposit_report')"
                :title="$t('public.sidebar.Deposit Report')"
                :active="route().current('transaction.deposit_report')"
            />
            <SidebarCollapsibleItem
                :href="route('transaction.withdrawal_report')"
                :title="$t('public.sidebar.Withdrawal Report')"
                :active="route().current('transaction.withdrawal_report')"
            />
            <SidebarCollapsibleItem
                :href="route('transaction.internal_transfer_report')"
                :title="$t('public.sidebar.Internal Transfer Report')"
                :active="route().current('transaction.internal_transfer_report')"
            />
            <SidebarCollapsibleItem
                :href="route('transaction.wallet_report')"
                :title="$t('public.sidebar.All Wallet Report')"
                :active="route().current('transaction.wallet_report')"
            />
        </SidebarCollapsible>

        <!-- platform configuration -->
        <SidebarCollapsible
            :title="$t('public.sidebar.Platform Configuration')"
            :active="route().current('platform_configuration.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-laptop-file"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('platform_configuration.ctrader')"
                :title="$t('public.sidebar.CTrader')"
                :active="route().current('platform_configuration.ctrader')"
            />
        </SidebarCollapsible>

        <!-- setting -->
        <SidebarCollapsible
            :title="$t('public.sidebar.Setting')"
            :active="route().current('setting.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-gear"
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

<!--            <SidebarCollapsibleItem-->
<!--                :href="route('setting.trading_account_setting')"-->
<!--                title="Trading Account Setting"-->
<!--                :active="route().current('setting.trading_account_setting')"-->
<!--            />-->
            <SidebarCollapsibleItem
                :href="route('setting.highlights_setting')"
                :title="$t('public.sidebar.Highlights')"
                :active="route().current('setting.highlights_setting')"
            />
        </SidebarCollapsible>

<!--        <SidebarCollapsible-->
<!--            title="Components"-->
<!--            :active="route().current('components.*')"-->
<!--        >-->
<!--            <template #icon>-->
<!--                <TemplateIcon-->
<!--                    class="flex-shrink-0 w-6 h-6"-->
<!--                    aria-hidden="true"-->
<!--                />-->
<!--            </template>-->

<!--            <SidebarCollapsibleItem-->
<!--                :href="route('components.buttons')"-->
<!--                title="Buttons"-->
<!--                :active="route().current('components.buttons')"-->
<!--            />-->
<!--        </SidebarCollapsible>-->

        <!-- Examples -->
        <!--
        => External link example
        <SidebarLink
            title="Github"
            href="https://github.com/kamona-wd/kui-laravel-breeze"
            external
            target="_blank"
        >
        </SidebarLink>

        => Collapsible examples
        <SidebarCollapsible title="Users" :active="$page.url.startsWith('/users')">
            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="$page.url === '/users/index'" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="$page.url === '/users/create'" />
        </SidebarCollapsible>

        <SidebarCollapsible title="Users" :active="usePage().url.value.startsWith('/users')">
            <template #icon>
                <UserIcon
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="route().current('users.index')" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="route().current('users.create')" />
        </SidebarCollapsible>-->
    </PerfectScrollbar>
</template>

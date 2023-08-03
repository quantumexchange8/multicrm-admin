<script setup>
import PerfectScrollbar from '@/Components/PerfectScrollbar.vue'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { TemplateIcon } from '@heroicons/vue/outline'
import { library } from '@fortawesome/fontawesome-svg-core'
/* import specific icons */
import { faGaugeHigh, faUser, faUserTie } from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
library.add(faGaugeHigh, faUser, faUserTie)

</script>

<template>
    <PerfectScrollbar
        tagname="nav"
        aria-label="main"
        class="relative flex flex-col flex-1 max-h-full gap-4 px-3"
    >
        <SidebarLink
            title="Dashboard"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-gauge-high"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <!-- member -->
        <SidebarCollapsible
            title="Members"
            :active="route().current('member.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-user"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('member.member_listing')"
                title="Member Listing"
                :active="route().current('member.member_listing')"
            />

            <SidebarCollapsibleItem
                :href="route('member.trading_account_listing')"
                title="Trading Account Listing"
                :active="route().current('member.trading_account_listing')"
            />

            <SidebarCollapsibleItem
                :href="route('member.network_tree')"
                title="Member Tree"
                :active="route().current('member.network_tree')"
            />

            <SidebarCollapsibleItem
                :href="route('member.rebate_allocation')"
                title="Rebate Allocation"
                :active="route().current('member.rebate_allocation')"
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

<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Action from "@/Pages/Setting/TradingAccount/Action.vue";
</script>

<template>
    <AuthenticatedLayout :title="$t('public.Trading Account Setting')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Trading Account Setting') }}
                </h2>
            </div>
        </template>

        <div class="p-4 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Setting Name') }}
                        </th>
                        <th scope="col" class="px-4 py-3">
                            {{ $t('public.Action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="bg-white even:dark:bg-transparent odd:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="p-4 font-thin rounded-l-full">
                            {{ $t('public.Group') }}
                        </th>
                        <td class="p-4 rounded-r-full">
                            <Action />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

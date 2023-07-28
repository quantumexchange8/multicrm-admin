<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { MoonIcon, SunIcon } from '@heroicons/vue/outline'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import PageFooter from '@/Components/PageFooter.vue'
import Button from '@/Components/Button.vue'
import { toggleDarkMode, isDark } from '@/Composables'
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";

defineProps({
    title: String
})
</script>

<template>
    <Head :title="title" />

    <div class="flex flex-col items-center justify-center min-h-screen overflow-hidden gap-4 py-6" style="background: linear-gradient(90deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.95) 81.88%, #000 100%), url('/assets/auth-bg.png'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed; box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);">

        <main class="flex items-center w-full sm:max-w-2xl bg-[#0f0f0f99]">
            <div
                class="w-full px-6 py-4 bg-transparent shadow-md sm:rounded-lg dark:bg-transparent"
            >
                <div class="flex justify-center">
                    <Link href="/">
                        <img src="/assets/logo.png" alt="Logo" class="flex-shrink-0" style="width: 300px" />
                    </Link>
                </div>
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <div class="flex justify-end mb-4">
                            <span class="inline-flex rounded-md">
                            <button
                                type="button"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:ring focus:ring-gray-500 focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark-eval-1 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <img src="/assets/lang_icon.png" alt="lang icon" style="width: 20px; margin-right: 10px">
                                Language
                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </span>
                        </div>
                    </template>
                    <template #content>
                        <DropdownLink @click="loadLanguageAsync('en')" href="#">English</DropdownLink>
<!--                        <DropdownLink @click="loadLanguageAsync('zh-Hans')" href="#">中文 (简)</DropdownLink>-->
<!--                        <DropdownLink @click="loadLanguageAsync('zh-Hant')" href="#">中文 (繁)</DropdownLink>-->
<!--                        <DropdownLink @click="loadLanguageAsync('vn')" href="#">Vietnamese</DropdownLink>-->
                    </template>
                </Dropdown>
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
defineProps({
    value: String,
})
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-white">
    <span v-if="value">
      <span class="text-red-500">*</span> {{ value }}
    </span>
        <span v-else>
      <slot />
    </span>
    </label>
</template>


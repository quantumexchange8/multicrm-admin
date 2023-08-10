<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: String,
})

defineEmits(['update:modelValue'])

const textarea = ref(null)

const focus = () => textarea.value?.focus()

defineExpose({
    textarea,
    focus
})

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus()
    }
})
</script>

<template>
    <textarea
        ref="textarea"
        rows="4"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-gray-500 focus:border-gray-500 dark:border-0 dark:bg-dark-eval-2 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
        placeholder="Write your thoughts here..."
    ></textarea>
</template>

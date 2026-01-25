<template>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">
            {{ label }}
        </label>

        <!-- Tags Display -->
        <div v-if="modelValue.length > 0" class="flex flex-wrap gap-2 mb-3">
            <span
                v-for="(tag, index) in modelValue"
                :key="index"
                class="inline-flex items-center space-x-2 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium"
            >
                <span>{{ tag }}</span>
                <button
                    type="button"
                    @click="removeTag(index)"
                    class="text-blue-600 hover:text-blue-800 transition"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </span>
        </div>

        <!-- Input -->
        <div v-if="!disabled" class="relative">
            <input
                v-model="inputValue"
                type="text"
                :placeholder="placeholder"
                @keydown.enter.prevent="addTag"
                @keydown.comma.prevent="addTag"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
            />
            <button
                v-if="inputValue.trim()"
                type="button"
                @click="addTag"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-blue-700 transition"
            >
                Add
            </button>
        </div>

        <p v-if="!disabled" class="mt-1 text-xs text-gray-500">
            Press Enter or comma to add
        </p>
        <p
            v-if="disabled && modelValue.length === 0"
            class="text-sm text-gray-500 italic"
        >
            None specified
        </p>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    label: String,
    placeholder: String,
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const inputValue = ref("");

const addTag = () => {
    if (props.disabled) return;
    const value = inputValue.value.trim();
    if (value && !props.modelValue.includes(value)) {
        emit("update:modelValue", [...props.modelValue, value]);
        inputValue.value = "";
    }
};

const removeTag = (index) => {
    if (props.disabled) return;
    const newTags = [...props.modelValue];
    newTags.splice(index, 1);
    emit("update:modelValue", newTags);
};
</script>

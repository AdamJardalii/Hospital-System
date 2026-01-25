<template>
    <span :class="badgeClasses">
        {{ text }}
    </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    text: String,
    variant: {
        type: String,
        default: "primary",
        validator: (value) =>
            ["primary", "secondary", "success", "warning", "danger"].includes(
                value,
            ),
    },
});

const badgeClasses = computed(() => {
    const baseClasses =
        "inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold";

    const variants = {
        primary: "bg-blue-100 text-blue-700",
        secondary: "bg-gray-100 text-gray-700",
        success: "bg-green-100 text-green-700",
        warning: "bg-yellow-100 text-yellow-700",
        danger: "bg-red-100 text-red-700",
    };

    return `${baseClasses} ${variants[props.variant]}`;
});
</script>

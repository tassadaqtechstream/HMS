<script setup>
import { ref, watch } from 'vue';

const props = defineProps({ message: String });
const isVisible = ref(!!props.message);

watch(() => props.message, (newVal) => {
    isVisible.value = !!newVal;
    if (newVal) {
        setTimeout(() => isVisible.value = false, 4000); // Auto-dismiss after 4s
    }
});
</script>

<template>
    <transition name="fade">
        <div v-if="isVisible" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ message }}
            <button @click="isVisible = false" class="ml-4 text-white font-bold">×</button>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>

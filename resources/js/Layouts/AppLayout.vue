<script setup lang="ts">
import AppHeader from '../Components/AppHeader.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-100">
        <!-- Header dipakai di semua halaman yang menggunakan layout ini -->
        <AppHeader />

        <!-- Slot konten halaman -->
        <main class="mx-auto max-w-5xl px-6 py-10">
            <slot />
        </main>
    </div>
</template>

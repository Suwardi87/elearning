<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

// Props ini dikirim dari HomeController melalui Inertia::render(...).
defineProps<{
    heroTitle: string;
    heroDescription: string;
    learningSteps: string[];
}>();
</script>

<template>
    <Head title="Home" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <!-- Breadcrumb sederhana untuk konsistensi dengan halaman lain -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <span class="font-medium text-slate-700">Home</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">SaaS-style Learning Platform</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ heroTitle }}</h2>
            <p class="mt-2 text-slate-600">{{ heroDescription }}</p>

            <!-- CTA utama ke halaman Course List -->
            <Link
                href="/courses"
                class="mt-6 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
            >
                Lihat Courses
            </Link>
        </section>

        <section class="mt-6 rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <h3 class="text-xl font-semibold text-slate-900">Alur Belajar Tahap Awal</h3>

            <ol class="mt-4 list-decimal space-y-2 pl-5 text-slate-700">
                <li v-for="(step, index) in learningSteps" :key="index">
                    {{ step }}
                </li>
            </ol>
        </section>
    </AppLayout>
</template>

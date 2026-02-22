<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

type CourseSummary = {
    id: number;
    title: string;
    total_lessons: number;
};

type TutorialItem = {
    id: number;
    lesson_number: number;
    title: string;
    estimated_minutes: number;
    is_completed: boolean;
};

type CourseProgress = {
    completed_lessons: number;
    total_lessons: number;
    percentage: number;
};

defineProps<{
    course: CourseSummary;
    progress: CourseProgress;
    tutorials: TutorialItem[];
}>();
</script>

<template>
    <Head :title="`Materi - ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Beranda</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Kursus</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}`" class="hover:text-slate-700">Detail Kursus</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Materi</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Daftar Materi</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ course.title }}</h2>
            <p class="mt-2 text-slate-600">Total materi: {{ course.total_lessons }}</p>
            <div class="mt-4 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                <p class="text-sm font-semibold text-indigo-900">
                    Progress Belajar: {{ progress.completed_lessons }} / {{ progress.total_lessons }} materi ({{ progress.percentage }}%)
                </p>
                <div class="mt-2 h-2 w-full rounded-full bg-indigo-100">
                    <div
                        class="h-2 rounded-full bg-indigo-600 transition-all duration-300"
                        :style="{ width: `${progress.percentage}%` }"
                    />
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <article
                    v-for="tutorial in tutorials"
                    :key="tutorial.id"
                    class="rounded-lg border border-slate-200 bg-slate-50 p-4"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">Materi {{ tutorial.lesson_number }}</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ tutorial.title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">Estimasi belajar: {{ tutorial.estimated_minutes }} menit</p>
                    <p
                        class="mt-1 text-xs font-semibold"
                        :class="tutorial.is_completed ? 'text-emerald-700' : 'text-amber-700'"
                    >
                        {{ tutorial.is_completed ? 'Status: Selesai' : 'Status: Belum Selesai' }}
                    </p>

                    <Link
                        :href="`/courses/${course.id}/tutorials/${tutorial.id}`"
                        class="mt-3 inline-flex rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                    >
                        Buka Tutorial
                    </Link>
                </article>
            </div>

            <Link
                :href="`/courses/${course.id}`"
                class="mt-6 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Detail Kursus
            </Link>
        </section>
    </AppLayout>
</template>

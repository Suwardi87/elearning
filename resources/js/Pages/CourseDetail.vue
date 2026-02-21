<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

type Course = {
    id: number;
    title: string;
    description: string;
    level: string;
    total_lessons: number;
};

// Props "course" dikirim dari CourseController@show.
defineProps<{
    course: Course;
}>();

// Samakan warna badge level dengan halaman list.
const levelBadgeClass = (level: Course['level']): string => {
    if (level === 'Beginner') return 'bg-emerald-50 text-emerald-700';
    if (level === 'Intermediate') return 'bg-amber-50 text-amber-700';
    if (level === 'Advanced') return 'bg-rose-50 text-rose-700';

    return 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <Head :title="`Course: ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <!-- Breadcrumb untuk bantu user memahami posisi halaman -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Home</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Courses</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Detail</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Course Detail</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ course.title }}</h2>
            <p class="mt-2 text-slate-600">{{ course.description }}</p>

            <div class="mt-6 flex flex-wrap gap-2 text-sm">
                <span :class="['rounded px-3 py-1 font-medium', levelBadgeClass(course.level)]">Level: {{ course.level }}</span>
                <span class="rounded bg-slate-100 px-3 py-1 font-medium text-slate-700">
                    {{ course.total_lessons }} {{ course.total_lessons === 1 ? 'lesson' : 'lessons' }}
                </span>
            </div>

            <!-- Kembali ke daftar course -->
            <Link
                href="/courses"
                class="mt-6 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Courses
            </Link>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

type Course = {
    id: number;
    title: string;
    description: string;
    level: string;
    total_lessons: number;
    challenge_count: number;
};

type CourseProgress = {
    completed_lessons: number;
    total_lessons: number;
    percentage: number;
};

// Props "course" dikirim dari CourseController@show.
defineProps<{
    course: Course;
    progress: CourseProgress;
}>();

// Samakan warna badge level dengan halaman list.
const levelBadgeClass = (level: Course['level']): string => {
    if (level === 'Beginner') return 'bg-emerald-50 text-emerald-700';
    if (level === 'Intermediate') return 'bg-amber-50 text-amber-700';
    if (level === 'Advanced') return 'bg-rose-50 text-rose-700';

    return 'bg-slate-100 text-slate-700';
};

// Status singkat progres agar mudah dibaca mahasiswa.
const progressStatusLabel = (percentage: number): string => {
    if (percentage === 0) return 'Belum Mulai';
    if (percentage >= 100) return 'Selesai';

    return 'Sedang Belajar';
};

const progressStatusClass = (percentage: number): string => {
    if (percentage === 0) return 'bg-slate-100 text-slate-700';
    if (percentage >= 100) return 'bg-emerald-50 text-emerald-700';

    return 'bg-amber-50 text-amber-700';
};

// Label level untuk tampilan UI.
const levelLabel = (level: string): string => {
    if (level === 'Beginner') return 'Pemula';
    if (level === 'Intermediate') return 'Menengah';
    if (level === 'Advanced') return 'Lanjutan';

    return level;
};
</script>

<template>
    <Head :title="`Detail Kursus: ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <!-- Breadcrumb untuk bantu user memahami posisi halaman -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Beranda</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Kursus</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Detail Kursus</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Detail Kursus</p>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Tahap {{ course.id }}</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ course.title }}</h2>
            <p class="mt-2 text-slate-600">{{ course.description }}</p>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Level</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ levelLabel(course.level) }}</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Total Materi</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ course.total_lessons }}</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Total Tantangan</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ course.challenge_count }}</p>
                </article>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-sm">
                <span :class="['rounded px-3 py-1 font-medium', levelBadgeClass(course.level)]">Level: {{ levelLabel(course.level) }}</span>
                <span class="rounded bg-slate-100 px-3 py-1 font-medium text-slate-700">
                    {{ course.total_lessons }} materi
                </span>
                <span class="rounded bg-sky-50 px-3 py-1 font-medium text-sky-700">
                    {{ course.challenge_count }} tantangan
                </span>
            </div>

            <div class="mt-6 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-sm font-semibold text-indigo-900">
                        Progres Materi: {{ progress.completed_lessons }} / {{ progress.total_lessons }} ({{ progress.percentage }}%)
                    </p>
                    <span
                        :class="['rounded px-2 py-1 text-xs font-semibold', progressStatusClass(progress.percentage)]"
                    >
                        {{ progressStatusLabel(progress.percentage) }}
                    </span>
                </div>
                <div class="mt-2 h-2 w-full rounded-full bg-indigo-100">
                    <div
                        class="h-2 rounded-full bg-indigo-600 transition-all duration-300"
                        :style="{ width: `${progress.percentage}%` }"
                    />
                </div>
            </div>

            <Link
                :href="`/courses/${course.id}/tutorials`"
                class="mt-6 inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 sm:w-auto"
            >
                Buka Materi
            </Link>

            <Link
                :href="`/courses/${course.id}/challenges`"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-sky-300 px-4 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50 sm:w-auto"
            >
                Buka Tantangan
            </Link>

            <!-- Kembali ke daftar course -->
            <Link
                href="/courses"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Daftar Kursus
            </Link>
        </section>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

type CourseSummary = {
    id: number;
    title: string;
    total_lessons: number;
};

type TutorialDetail = {
    id: number;
    lesson_number: number;
    title: string;
    content: string;
    estimated_minutes: number;
    is_completed: boolean;
};

type TutorialNavigation = {
    id: number;
    lesson_number: number;
} | null;

type ContentBlock =
    | {
          type: 'text';
          value: string;
      }
    | {
          type: 'code';
          language: string;
          value: string;
      };

const props = defineProps<{
    course: CourseSummary;
    tutorial: TutorialDetail;
    previousTutorial: TutorialNavigation;
    nextTutorial: TutorialNavigation;
}>();

// Parse fenced code block ```lang ... ``` agar tampil rapi sebagai panel code.
const contentBlocks = computed<ContentBlock[]>(() => {
    const blocks: ContentBlock[] = [];
    const content = props.tutorial.content;
    const pattern = /```([\w+-]*)\n([\s\S]*?)```/g;

    let lastIndex = 0;
    let match: RegExpExecArray | null;

    while ((match = pattern.exec(content)) !== null) {
        const textPart = content.slice(lastIndex, match.index).trim();
        if (textPart) {
            blocks.push({
                type: 'text',
                value: textPart,
            });
        }

        blocks.push({
            type: 'code',
            language: match[1] || 'text',
            value: match[2].trimEnd(),
        });

        lastIndex = pattern.lastIndex;
    }

    const remainingText = content.slice(lastIndex).trim();
    if (remainingText) {
        blocks.push({
            type: 'text',
            value: remainingText,
        });
    }

    return blocks;
});
</script>

<template>
    <Head :title="`Lesson ${tutorial.lesson_number} - ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Home</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Courses</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}`" class="hover:text-slate-700">Detail</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}/tutorials`" class="hover:text-slate-700">Tutorials</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Lesson {{ tutorial.lesson_number }}</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Tutorial Detail</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ tutorial.title }}</h2>
            <p class="mt-2 text-sm text-slate-500">Lesson {{ tutorial.lesson_number }} dari {{ course.total_lessons }}</p>
            <p class="mt-1 text-sm font-medium text-slate-700">Estimasi belajar: {{ tutorial.estimated_minutes }} menit</p>
            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                <p
                    class="text-sm font-semibold"
                    :class="tutorial.is_completed ? 'text-emerald-700' : 'text-amber-700'"
                >
                    {{ tutorial.is_completed ? 'Status: Selesai' : 'Status: Belum Selesai' }}
                </p>

                <Link
                    v-if="!tutorial.is_completed"
                    :href="`/courses/${course.id}/tutorials/${tutorial.id}/complete`"
                    method="post"
                    as="button"
                    class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                >
                    Tandai Selesai
                </Link>

                <Link
                    v-else
                    :href="`/courses/${course.id}/tutorials/${tutorial.id}/complete`"
                    method="delete"
                    as="button"
                    class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batalkan Selesai
                </Link>
            </div>

            <div class="mt-4 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-sm font-semibold text-indigo-700">Cara Belajar Cepat</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-indigo-900">
                    <li>Lihat <strong>Kebutuhan</strong> dulu sebelum copy code.</li>
                    <li>Ikuti <strong>Contoh Source Code</strong> dan jalankan command.</li>
                    <li>Pastikan output sama dengan <strong>Hasil yang Harus Terlihat</strong>.</li>
                </ul>
            </div>

            <div class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-sm font-semibold text-emerald-700">Alur Belajar Mandiri</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-emerald-900">
                    <li>Baca <strong>Penjelasan Singkat</strong> (cukup 1 kali).</li>
                    <li>Kerjakan <strong>Contoh Source Code</strong> sampai jalan.</li>
                    <li>Lanjutkan <strong>Latihan Modul</strong> tanpa copy-paste penuh.</li>
                    <li>Jika error, cek <strong>Dokumentasi Resmi</strong> di bawah modul.</li>
                </ul>
            </div>

            <div class="mt-4 space-y-4">
                <template v-for="(block, index) in contentBlocks" :key="index">
                    <p
                        v-if="block.type === 'text'"
                        class="whitespace-pre-line text-slate-700"
                    >
                        {{ block.value }}
                    </p>

                    <div
                        v-else
                        class="overflow-x-auto rounded-lg border border-slate-800 bg-slate-900 p-4"
                    >
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-300">
                            {{ block.language }}
                        </p>
                        <pre class="text-sm text-slate-100"><code>{{ block.value }}</code></pre>
                    </div>
                </template>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <Link
                    v-if="previousTutorial"
                    :href="`/courses/${course.id}/tutorials/${previousTutorial.id}`"
                    class="inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
                >
                    Lesson Sebelumnya
                </Link>

                <Link
                    v-if="nextTutorial"
                    :href="`/courses/${course.id}/tutorials/${nextTutorial.id}`"
                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 sm:w-auto"
                >
                    Lesson Berikutnya
                </Link>
            </div>

            <Link
                :href="`/courses/${course.id}/tutorials`"
                class="mt-4 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Daftar Tutorial
            </Link>
        </section>
    </AppLayout>
</template>

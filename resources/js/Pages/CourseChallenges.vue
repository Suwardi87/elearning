<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { CourseSummary, ChallengeItem } from '@/types/course_challenge';



defineProps<{
    course: CourseSummary;
    challenges: ChallengeItem[];
}>();
</script>

<template>
    <Head :title="`Tantangan - ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Home</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Courses</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}`" class="hover:text-slate-700">Detail</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Challenges</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Challenge List</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ course.title }}</h2>
            <p class="mt-2 text-slate-600">Total tantangan: {{ course.challenge_count ?? challenges.length }}</p>

            <div
                v-if="challenges.length === 0"
                class="mt-6 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center"
            >
                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    Status: Belum tersedia
                </span>
                <p class="text-sm font-medium text-slate-700">Belum ada tantangan untuk course ini.</p>
                <p class="mt-1 text-xs text-slate-500">Admin belum menambahkan tantangan untuk course ini.</p>
                <Link
                    :href="`/courses/${course.id}`"
                    class="mt-4 inline-flex rounded-md border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                >
                    Kembali ke Detail Course
                </Link>
            </div>

            <div v-else class="mt-6 space-y-3">
                <article
                    v-for="challenge in challenges"
                    :key="challenge.id"
                    class="rounded-lg border border-slate-200 bg-slate-50 p-4"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">Challenge {{ challenge.step_number }}</p>
                    <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ challenge.title }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ challenge.instruction }}</p>

                    <Link
                        :href="`/courses/${course.id}/challenges/${challenge.id}`"
                        class="mt-3 inline-flex rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                    >
                        Kerjakan Tantangan
                    </Link>
                </article>
            </div>

            <Link
                :href="`/courses/${course.id}`"
                class="mt-6 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Detail Course
            </Link>
        </section>
    </AppLayout>
</template>

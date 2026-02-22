<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { ChallengeDetail, CourseSummary, PageProps } from '@/types/course_challenge_detail';


const props = defineProps<{
    course: CourseSummary;
    challenge: ChallengeDetail;
}>();

const page = usePage<PageProps>();
const flashSuccess = computed<string>(() => page.props.flash?.success ?? '');

const form = useForm({
    submitter_name: '',
    answer_text: '',
});

const submitAnswer = (): void => {
    form.post(`/courses/${props.course.id}/challenges/${props.challenge.id}/submit`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Tantangan ${challenge.step_number} - ${course.title}`" />

    <AppLayout>
        <section class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Home</Link>
                <span>/</span>
                <Link href="/courses" class="hover:text-slate-700">Courses</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}`" class="hover:text-slate-700">Detail</Link>
                <span>/</span>
                <Link :href="`/courses/${course.id}/challenges`" class="hover:text-slate-700">Challenges</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Submit</span>
            </nav>

            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Challenge Submission</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">{{ challenge.title }}</h2>
            <p class="mt-2 text-sm text-slate-500">Step {{ challenge.step_number }} | {{ course.title }}</p>
            <p class="mt-3 text-slate-700">{{ challenge.instruction }}</p>

            <div v-if="flashSuccess" class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ flashSuccess }}
            </div>

            <form class="mt-6 space-y-4" @submit.prevent="submitAnswer">
                <div>
                    <label for="submitter_name" class="text-sm font-medium text-slate-700">Nama</label>
                    <input
                        id="submitter_name"
                        v-model="form.submitter_name"
                        type="text"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700"
                        placeholder="Nama kamu"
                    >
                    <p v-if="form.errors.submitter_name" class="mt-1 text-xs text-rose-600">{{ form.errors.submitter_name }}</p>
                </div>

                <div>
                    <label for="answer_text" class="text-sm font-medium text-slate-700">Jawaban Tantangan</label>
                    <textarea
                        id="answer_text"
                        v-model="form.answer_text"
                        rows="8"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700"
                        placeholder="Tulis solusi atau penjelasan implementasi kamu di sini..."
                    />
                    <p v-if="form.errors.answer_text" class="mt-1 text-xs text-rose-600">{{ form.errors.answer_text }}</p>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 sm:w-auto"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Mengirim...' : 'Kirim Jawaban' }}
                </button>
            </form>

            <Link
                :href="`/courses/${course.id}/challenges`"
                class="mt-4 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
            >
                Kembali ke Daftar Tantangan
            </Link>
        </section>
    </AppLayout>
</template>

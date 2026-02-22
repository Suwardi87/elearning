<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { Course } from '@/types/course';



// Props dikirim dari CourseController (Laravel).
const props = defineProps<{
    courses: Course[];
    courseCount: number;
    levels: string[];
}>();

// Filter level sederhana di sisi frontend.
const selectedLevel = ref<string>('All');
const searchQuery = ref<string>('');
const sortBy = ref<'default' | 'title_asc' | 'lessons_desc' | 'lessons_asc'>('default');

const resetFilters = (): void => {
    selectedLevel.value = 'All';
    searchQuery.value = '';
    sortBy.value = 'default';
};

const filteredCourses = computed<Course[]>(() => {
    const coursesByLevel =
        selectedLevel.value === 'All' ? props.courses : props.courses.filter((course) => course.level === selectedLevel.value);

    const keyword = searchQuery.value.trim().toLowerCase();

    const coursesByKeyword =
        keyword === '' ? coursesByLevel : coursesByLevel.filter((course) => course.title.toLowerCase().includes(keyword));

    const sortedCourses = [...coursesByKeyword];

    if (sortBy.value === 'title_asc') {
        sortedCourses.sort((a, b) => a.title.localeCompare(b.title));
    }

    if (sortBy.value === 'lessons_desc') {
        sortedCourses.sort((a, b) => b.total_lessons - a.total_lessons);
    }

    if (sortBy.value === 'lessons_asc') {
        sortedCourses.sort((a, b) => a.total_lessons - b.total_lessons);
    }

    return sortedCourses;
});

// Total materi dari hasil yang sedang terlihat setelah filter + sort.
const visibleLessonsCount = computed<number>(() => {
    return filteredCourses.value.reduce((total, course) => total + course.total_lessons, 0);
});

// Ringkasan total materi untuk semua jalur (tidak terpengaruh filter).
const totalLessonsCount = computed<number>(() => {
    return props.courses.reduce((total, course) => total + course.total_lessons, 0);
});

// Ringkasan total tantangan untuk semua jalur.
const totalChallengesCount = computed<number>(() => {
    return props.courses.reduce((total, course) => total + course.challenge_count, 0);
});

// Total tantangan dari hasil yang sedang terlihat setelah filter + sort.
const visibleChallengesCount = computed<number>(() => {
    return filteredCourses.value.reduce((total, course) => total + course.challenge_count, 0);
});

// Ringkasan filter aktif agar user tahu kondisi daftar saat ini.
const activeFilters = computed<string[]>(() => {
    const filters: string[] = [];

    if (selectedLevel.value !== 'All') {
        filters.push(`Level: ${levelLabel(selectedLevel.value)}`);
    }

    if (searchQuery.value.trim() !== '') {
        filters.push(`Kata kunci: "${searchQuery.value.trim()}"`);
    }

    if (sortBy.value === 'title_asc') {
        filters.push('Urutan: Judul A-Z');
    }

    if (sortBy.value === 'lessons_desc') {
        filters.push('Urutan: Materi Terbanyak');
    }

    if (sortBy.value === 'lessons_asc') {
        filters.push('Urutan: Materi Tersedikit');
    }

    return filters;
});

const hasActiveFilters = computed<boolean>(() => {
    return selectedLevel.value !== 'All' || searchQuery.value.trim() !== '' || sortBy.value !== 'default';
});

const emptyStateText = computed<string>(() => {
    if (searchQuery.value.trim() !== '') {
        return `Tidak ada kursus yang cocok dengan kata kunci "${searchQuery.value.trim()}".`;
    }

    if (selectedLevel.value !== 'All') {
        return `Tidak ada kursus pada level ${levelLabel(selectedLevel.value)}.`;
    }

    return 'Coba ubah filter atau atur ulang ke Semua Level.';
});

// Warna badge berdasarkan level agar mudah dibedakan mahasiswa.
const levelBadgeClass = (level: Course['level']): string => {
    if (level === 'Beginner') return 'bg-emerald-50 text-emerald-700';
    if (level === 'Intermediate') return 'bg-amber-50 text-amber-700';
    if (level === 'Advanced') return 'bg-rose-50 text-rose-700';

    return 'bg-slate-100 text-slate-700';
};

// Label level untuk tampilan UI (nilai asli tetap dari backend).
const levelLabel = (level: string): string => {
    if (level === 'Beginner') return 'Pemula';
    if (level === 'Intermediate') return 'Menengah';
    if (level === 'Advanced') return 'Lanjutan';

    return level;
};
</script>

<template>
    <Head title="Jalur Belajar Coding Berbasis Project" />

    <AppLayout>
        <!-- Judul halaman -->
        <section class="mb-6">
            <!-- Breadcrumb untuk menjaga konsistensi alur navigasi -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Beranda</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Kursus</span>
            </nav>

            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Jalur Belajar Coding Berbasis Project</h2>
            <p class="mt-2 text-slate-600">
                Satu platform untuk kamu yang ingin belajar coding dengan arah jelas dan hasil nyata.
            </p>
            <p class="mt-1 text-sm text-slate-500">
                {{ courseCount }} tahap pembelajaran | {{ totalLessonsCount }} materi | {{ totalChallengesCount }} tantangan terstruktur
            </p>
            <p class="mt-1 text-sm text-slate-500">
                Tampilan saat ini: {{ filteredCourses.length }} dari {{ courseCount }} tahap | {{ visibleLessonsCount }} materi | {{ visibleChallengesCount }} tantangan
            </p>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Tahap Belajar</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ courseCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Disusun dari dasar sampai full project.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Total Materi</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ totalLessonsCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Materi bertahap dengan contoh kode nyata.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Total Tantangan</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ totalChallengesCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Latihan praktik untuk menguji pemahamanmu.</p>
                </article>
            </div>

            <!-- Pencarian dan filter -->
            <div class="mt-5">
                <label for="course-search" class="text-sm font-medium text-slate-700">Cari Kursus</label>
                <div class="mt-1 flex flex-col gap-2 md:flex-row md:items-center">
                    <input
                        id="course-search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Contoh: Laravel, Vue, atau PHP"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >

                    <button
                        v-if="searchQuery.trim() !== ''"
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 md:w-auto"
                        @click="searchQuery = ''"
                    >
                        Hapus Pencarian
                    </button>

                    <select
                        id="level-filter"
                        v-model="selectedLevel"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >
                        <option value="All">Semua Level</option>
                        <option v-for="level in levels" :key="level" :value="level">
                            {{ levelLabel(level) }}
                        </option>
                    </select>

                    <select
                        id="sort-by"
                        v-model="sortBy"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >
                        <option value="default">Urutan Bawaan</option>
                        <option value="title_asc">Judul A-Z</option>
                        <option value="lessons_desc">Materi Terbanyak</option>
                        <option value="lessons_asc">Materi Tersedikit</option>
                    </select>

                    <!-- Tombol cepat untuk mengembalikan filter ke semua level -->
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 md:w-auto"
                        @click="resetFilters"
                    >
                        Atur Ulang Filter
                    </button>
                </div>

                <p v-if="activeFilters.length > 0" class="mt-2 text-xs text-slate-500">
                    Filter yang sedang dipakai: {{ activeFilters.join(' | ') }}
                </p>
            </div>
        </section>

        <!-- Daftar course dalam bentuk card -->
        <section v-if="filteredCourses.length > 0" class="grid gap-4 md:grid-cols-2">
            <article
                v-for="course in filteredCourses"
                :key="course.id"
                class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Tahap {{ course.id }}</p>
                <h3 class="text-xl font-semibold text-slate-900">{{ course.title }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ course.description }}</p>

                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <span :class="['rounded px-3 py-1 font-medium', levelBadgeClass(course.level)]">Level: {{ levelLabel(course.level) }}</span>
                    <span class="rounded bg-slate-100 px-3 py-1 font-medium text-slate-700">
                        {{ course.total_lessons }} materi
                    </span>
                    <span class="rounded bg-sky-50 px-3 py-1 font-medium text-sky-700">
                        {{ course.challenge_count }} tantangan
                    </span>
                </div>

                <!-- Tombol menuju halaman detail course -->
                <Link
                    :href="`/courses/${course.id}`"
                    class="mt-5 inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 sm:w-auto"
                >
                    Lihat Detail
                </Link>
            </article>
        </section>

        <!-- Tampilan fallback jika hasil filter kosong -->
        <section v-else class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <h3 class="text-lg font-semibold text-slate-900">Kursus tidak ditemukan</h3>
            <p class="mt-2 text-sm text-slate-600">{{ emptyStateText }}</p>

            <button
                type="button"
                class="mt-4 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
                @click="resetFilters"
            >
                Atur Ulang Semua Filter
            </button>
        </section>
    </AppLayout>
</template>

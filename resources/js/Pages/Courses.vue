<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

type Course = {
    id: number;
    title: string;
    description: string;
    level: string;
    total_lessons: number;
};

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

// Ringkasan filter aktif agar user tahu kondisi daftar saat ini.
const activeFilters = computed<string[]>(() => {
    const filters: string[] = [];

    if (selectedLevel.value !== 'All') {
        filters.push(`Level: ${selectedLevel.value}`);
    }

    if (searchQuery.value.trim() !== '') {
        filters.push(`Keyword: "${searchQuery.value.trim()}"`);
    }

    if (sortBy.value === 'title_asc') {
        filters.push('Urutan: Title A-Z');
    }

    if (sortBy.value === 'lessons_desc') {
        filters.push('Urutan: Lessons Terbanyak');
    }

    if (sortBy.value === 'lessons_asc') {
        filters.push('Urutan: Lessons Tersedikit');
    }

    return filters;
});

const hasActiveFilters = computed<boolean>(() => {
    return selectedLevel.value !== 'All' || searchQuery.value.trim() !== '' || sortBy.value !== 'default';
});

// Warna badge berdasarkan level agar mudah dibedakan mahasiswa.
const levelBadgeClass = (level: Course['level']): string => {
    if (level === 'Beginner') return 'bg-emerald-50 text-emerald-700';
    if (level === 'Intermediate') return 'bg-amber-50 text-amber-700';
    if (level === 'Advanced') return 'bg-rose-50 text-rose-700';

    return 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <Head title="Course List" />

    <AppLayout>
        <!-- Judul halaman -->
        <section class="mb-6">
            <!-- Breadcrumb untuk menjaga konsistensi alur navigasi -->
            <nav class="mb-4 flex items-center gap-2 text-xs text-slate-500">
                <Link href="/" class="hover:text-slate-700">Home</Link>
                <span>/</span>
                <span class="font-medium text-slate-700">Courses</span>
            </nav>

            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Course List</h2>
            <p class="mt-2 text-slate-600">
                Pilih course untuk memulai jalur belajar project-based. | Total: {{ courseCount }} {{ courseCount === 1 ? 'course' : 'courses' }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
                Menampilkan: {{ filteredCourses.length }} dari {{ courseCount }} {{ courseCount === 1 ? 'course' : 'courses' }}
            </p>

            <!-- Filter level -->
            <div class="mt-4">
                <label for="course-search" class="text-sm font-medium text-slate-700">Cari Course</label>
                <div class="mt-1 flex flex-col gap-2 md:flex-row md:items-center">
                    <input
                        id="course-search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Contoh: Laravel"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >

                    <button
                        v-if="searchQuery.trim() !== ''"
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 md:w-auto"
                        @click="searchQuery = ''"
                    >
                        Clear Search
                    </button>

                    <select
                        id="level-filter"
                        v-model="selectedLevel"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >
                        <option value="All">All Levels</option>
                        <option v-for="level in levels" :key="level" :value="level">
                            {{ level }}
                        </option>
                    </select>

                    <select
                        id="sort-by"
                        v-model="sortBy"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 md:w-64"
                    >
                        <option value="default">Default Order</option>
                        <option value="title_asc">Title A-Z</option>
                        <option value="lessons_desc">Lessons Terbanyak</option>
                        <option value="lessons_asc">Lessons Tersedikit</option>
                    </select>

                    <!-- Tombol cepat untuk mengembalikan filter ke semua level -->
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 md:w-auto"
                        @click="resetFilters"
                    >
                        Reset Filter
                    </button>
                </div>

                <p v-if="activeFilters.length > 0" class="mt-2 text-xs text-slate-500">
                    Filter aktif: {{ activeFilters.join(' | ') }}
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
                <h3 class="text-xl font-semibold text-slate-900">{{ course.title }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ course.description }}</p>

                <div class="mt-4 flex flex-wrap gap-2 text-xs">
                    <span :class="['rounded px-3 py-1 font-medium', levelBadgeClass(course.level)]">Level: {{ course.level }}</span>
                    <span class="rounded bg-slate-100 px-3 py-1 font-medium text-slate-700">
                        {{ course.total_lessons }} {{ course.total_lessons === 1 ? 'lesson' : 'lessons' }}
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
            <h3 class="text-lg font-semibold text-slate-900">Course tidak ditemukan</h3>
            <p class="mt-2 text-sm text-slate-600">Coba pilih level lain atau reset ke All Levels.</p>

            <button
                type="button"
                class="mt-4 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto"
                @click="resetFilters"
            >
                Reset Semua Filter
            </button>
        </section>
    </AppLayout>
</template>

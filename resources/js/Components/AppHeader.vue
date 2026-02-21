<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type SharedProps = {
    appName?: string;
    appTagline?: string;
};

const page = usePage<SharedProps>();

// Ambil shared props dari middleware Laravel.
const appName = computed<string>(() => page.props.appName ?? 'E-Learning');
const appTagline = computed<string>(() => page.props.appTagline ?? 'Belajar sambil membangun produk');

// Cek menu aktif berdasarkan path saat ini.
const isActive = (path: string): boolean => {
    const currentPath = page.url.split('?')[0];

    // Menu "Courses" aktif untuk list dan detail: /courses dan /courses/{id}
    if (path === '/courses') {
        return currentPath === '/courses' || currentPath.startsWith('/courses/');
    }

    return currentPath === path;
};
</script>

<template>
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ appName }}</h1>
                <p class="text-sm text-slate-500">{{ appTagline }}</p>
            </div>

            <nav class="flex items-center gap-2 text-sm">
                <Link
                    href="/"
                    :class="[
                        'rounded px-3 py-1 font-medium transition',
                        isActive('/') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                    ]"
                >
                    Home
                </Link>
                <Link
                    href="/courses"
                    :class="[
                        'rounded px-3 py-1 font-medium transition',
                        isActive('/courses') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                    ]"
                >
                    Courses
                </Link>
            </nav>
        </div>
    </header>
</template>

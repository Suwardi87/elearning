<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseTutorial;
use Illuminate\Database\Seeder;

class CourseTutorialSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();
        $payloads = [];

        Course::query()->select(['id', 'title', 'total_lessons'])->get()->each(function (Course $course) use (&$payloads, $now): void {
            for ($lesson = 1; $lesson <= $course->total_lessons; $lesson++) {
                $topic = $this->resolveTopic($course->title, $lesson);

                $payloads[] = [
                    'course_id' => $course->id,
                    'lesson_number' => $lesson,
                    'title' => "Lesson {$lesson}: {$topic}",
                    'content' => $this->buildLessonContent($course->title, $lesson, $topic),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        });

        CourseTutorial::query()->upsert(
            $payloads,
            ['course_id', 'lesson_number'],
            ['title', 'content', 'updated_at']
        );
    }

    /**
     * Ambil topik lesson berdasarkan nama course.
     * Jika lesson melebihi daftar topik, gunakan fallback otomatis.
     */
    private function resolveTopic(string $courseTitle, int $lessonNumber): string
    {
        $topics = $this->courseTopics()[$courseTitle] ?? [];

        return $topics[$lessonNumber - 1] ?? "Praktik Step {$lessonNumber}";
    }

    /**
     * Susun konten lesson dengan pola tetap supaya mahasiswa mudah mengikuti.
     */
    private function buildLessonContent(string $courseTitle, int $lessonNumber, string $topic): string
    {
        return implode("\n", [
            "Modul {$lessonNumber}: {$topic}",
            "",
            "Kebutuhan:",
            $this->buildModuleNeeds($courseTitle, $topic),
            "",
            "Penjelasan Singkat:",
            $this->buildModuleShortExplanation($courseTitle, $topic),
            "",
            "Contoh Source Code:",
            $this->buildCodeExample($courseTitle, $topic),
            "",
            "Cara Menjalankan:",
            $this->buildRunInstructions($courseTitle, $topic),
            "",
            "Hasil yang Harus Terlihat:",
            $this->buildExpectedOutput($courseTitle, $topic),
            "",
            "Latihan Modul:",
            $this->buildFromScratchTask($courseTitle, $lessonNumber, $topic),
            "",
            "Dokumentasi Resmi:",
            $this->buildOfficialDocsReference($courseTitle, $topic),
        ]);
    }

    /**
     * Kebutuhan minimum per modul supaya langsung praktik.
     */
    private function buildModuleNeeds(string $courseTitle, string $topic): string
    {
        if ($courseTitle === 'PHP Dasar by Project') {
            return implode("\n", [
                '- PHP 8.2+ terpasang (`php -v`).',
                '- MySQL/MariaDB aktif untuk latihan query.',
                '- File `config/database.php` berisi host, dbname, user, password.',
                '- Topik modul: '.$topic,
            ]);
        }

        if ($courseTitle === 'Vue 3 Dasar by Project') {
            return implode("\n", [
                '- Node.js 20+ terpasang.',
                '- Jalankan `npm install` dulu.',
                '- Topik modul: '.$topic,
            ]);
        }

        if (
            $courseTitle === 'Laravel Dasar by Project'
            || $courseTitle === 'Inertia.js Dasar untuk Laravel'
            || $courseTitle === 'Full Project: Laravel + Inertia + Vue'
        ) {
            return implode("\n", [
                '- Composer + PHP 8.2+ siap.',
                '- `.env` database sudah benar.',
                '- Jalankan `php artisan migrate --seed` bila perlu data awal.',
                '- Topik modul: '.$topic,
            ]);
        }

        return implode("\n", [
            '- Environment coding siap.',
            '- Topik modul: '.$topic,
        ]);
    }

    /**
     * Penjelasan sengaja dibuat ringkas: konsep inti dalam 1-2 kalimat.
     */
    private function buildModuleShortExplanation(string $courseTitle, string $topic): string
    {
        if ($courseTitle === 'PHP Dasar by Project') {
            return 'Di modul ini kamu fokus alur dasar: koneksi database, jalankan query, lalu tampilkan hasil ke layar.';
        }

        if ($courseTitle === 'Vue 3 Dasar by Project') {
            return 'Di modul ini kamu membuat UI interaktif kecil memakai state lokal (`ref`) dan event.';
        }

        if ($courseTitle === 'Laravel Dasar by Project') {
            return 'Di modul ini kamu susun alur server-side: route -> controller -> service/request -> response.';
        }

        if ($courseTitle === 'Inertia.js Dasar untuk Laravel') {
            return 'Di modul ini kamu kirim data dari Laravel ke Vue page lewat Inertia tanpa API REST terpisah.';
        }

        if ($courseTitle === 'Full Project: Laravel + Inertia + Vue') {
            return 'Di modul ini kamu integrasikan backend + frontend jadi fitur nyata yang bisa diuji user.';
        }

        return 'Fokus ke satu konsep inti, jalankan, cek hasil, lalu lanjut modul berikutnya.';
    }

    /**
     * Step run singkat agar user tahu command yang dipakai.
     */
    private function buildRunInstructions(string $courseTitle, string $topic): string
    {
        if ($courseTitle === 'PHP Dasar by Project') {
            return implode("\n", [
                "1. Simpan file modul, misal `modules/lesson.php`.",
                '2. Jalankan `php modules/lesson.php`.',
                '3. Cek output terminal sesuai bagian hasil.',
                '4. Jika modul pakai query, pastikan tabel sudah dibuat.',
            ]);
        }

        if ($courseTitle === 'Vue 3 Dasar by Project') {
            return implode("\n", [
                '1. Jalankan `npm install`.',
                '2. Jalankan `npm run dev`.',
                '3. Buka browser dan cek komponen berjalan.',
            ]);
        }

        if (
            $courseTitle === 'Laravel Dasar by Project'
            || $courseTitle === 'Inertia.js Dasar untuk Laravel'
            || $courseTitle === 'Full Project: Laravel + Inertia + Vue'
        ) {
            return implode("\n", [
                '1. Jalankan `php artisan serve`.',
                '2. Jalankan `npm run dev` di terminal lain.',
                '3. Buka browser sesuai route modul.',
                '4. Cek data tampil sesuai props/controller.',
            ]);
        }

        return implode("\n", [
            '1. Jalankan kode modul.',
            '2. Cek hasil dan lanjut ke modul berikutnya.',
        ]);
    }

    /**
     * Prasyarat singkat agar user tahu bekal yang harus dipahami.
     */
    private function buildPrerequisite(string $courseTitle, int $lessonNumber): string
    {
        if ($lessonNumber === 1) {
            return implode("\n", [
                "- Tidak ada prasyarat materi.",
                "- Pastikan environment coding sudah siap.",
            ]);
        }

        $previousTopic = $this->courseTopics()[$courseTitle][$lessonNumber - 2] ?? null;

        return implode("\n", [
            '- Sudah paham lesson sebelumnya: '.($previousTopic ?? 'lesson sebelumnya'),
            "- Sudah mencoba minimal 1 contoh kode dari lesson sebelumnya.",
        ]);
    }

    /**
     * Checkpoint pemahaman cepat ala quiz refleksi.
     */
    private function buildCheckpointQuestions(string $topic): string
    {
        return implode("\n", [
            "1. Apa tujuan utama topik \"{$topic}\"?",
            "2. Bisakah kamu jelaskan alur contoh kode dengan kata-katamu sendiri?",
            "3. Bisakah kamu ubah contoh kode lalu tetap berjalan tanpa error?",
        ]);
    }

    /**
     * Misi mandiri agar user benar-benar membangun dari nol.
     */
    private function buildFromScratchTask(string $courseTitle, int $lessonNumber, string $topic): string
    {
        return implode("\n", [
            "- Buat ulang lesson {$lessonNumber} ({$topic}) di file/folder baru tanpa copy-paste penuh.",
            '- Wajib ubah minimal 1 requirement: nama fitur, field, atau alur.',
            '- Commit hasil dengan format pesan: `lesson-'.$lessonNumber.': build from scratch`.',
            '- Jika buntu, baca dulu dokumentasi resmi sebelum melihat contoh ulang.',
            '- Catat 1 hal yang kamu pelajari + 1 error yang berhasil kamu selesaikan.',
            '- Fokus track: '.$this->resolveProjectModule($courseTitle),
        ]);
    }

    /**
     * Definisi selesai lesson agar standar belajar jelas.
     */
    private function buildLessonCompletionCriteria(string $courseTitle, string $topic): string
    {
        return implode("\n", [
            '- Kode berjalan normal tanpa warning/error.',
            "- Kamu bisa jelaskan alur topik \"{$topic}\" tanpa membaca catatan.",
            '- Kamu bisa modifikasi kecil fitur dan hasil tetap benar.',
            '- Kode sudah dirapikan (nama variabel jelas, struktur mudah dibaca).',
            '- Relevan dengan jalur project: '.$this->resolveProjectModule($courseTitle),
        ]);
    }

    /**
     * Error umum berdasarkan jalur belajar.
     */
    private function buildCommonErrors(string $courseTitle, string $topic): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project',
            'OOP PHP Dasar by Project',
            'MVC Dasar (PHP Native) by Project' => implode("\n", [
                "- Error syntax karena titik koma terlupa.",
                "- Nama variabel tidak konsisten huruf besar/kecil.",
                "- Solusi: jalankan `php -l nama_file.php` sebelum run utama.",
            ]),
            'Vue 3 Dasar by Project' => implode("\n", [
                "- Data tidak reaktif karena tidak pakai `ref`/`reactive`.",
                "- Event tidak jalan karena salah penulisan `@click`.",
                "- Solusi: cek console browser + pastikan binding template benar.",
            ]),
            'Laravel Dasar by Project' => implode("\n", [
                "- Route tidak ketemu karena path atau method salah.",
                "- Data tidak tampil karena controller tidak kirim payload lengkap.",
                "- Solusi: cek `php artisan route:list` dan logika controller.",
            ]),
            'Inertia.js Dasar untuk Laravel',
            'Full Project: Laravel + Inertia + Vue' => implode("\n", [
                "- Page Vue tidak ditemukan karena nama `Inertia::render()` tidak sesuai file.",
                "- Form tidak update karena validasi backend gagal tapi error tidak ditampilkan.",
                "- Solusi: cek nama page, props, dan object `errors` di Inertia.",
            ]),
            default => implode("\n", [
                "- Error umum pada topik {$topic}.",
                "- Solusi: jalankan ulang contoh minimum dan cek pesan error pertama.",
            ]),
        };
    }

    /**
     * Referensi dokumentasi resmi per track agar materi selalu update.
     */
    private function buildOfficialDocsReference(string $courseTitle, string $topic): string
    {
        $topicReference = $this->buildTopicDocsReference($courseTitle, $topic);

        $trackReferences = match ($courseTitle) {
            'PHP Dasar by Project',
            'OOP PHP Dasar by Project',
            'MVC Dasar (PHP Native) by Project' => [
                "- https://www.php.net/manual/en/tutorial.php",
                "- https://www.php.net/manual/en/language.control-structures.php",
                "- https://www.php.net/manual/en/functions.user-defined.php",
                "- https://www.php.net/manual/en/book.classobj.php",
            ],
            'Vue 3 Dasar by Project' => [
                "- https://vuejs.org/guide/introduction",
                "- https://vuejs.org/api/sfc-script-setup.html",
                "- https://vuejs.org/guide/essentials/reactivity-fundamentals",
                "- https://vuejs.org/guide/components/props",
                "- https://vuejs.org/guide/components/events",
            ],
            'Laravel Dasar by Project' => [
                "- https://laravel.com/docs/12.x/routing",
                "- https://laravel.com/docs/12.x/controllers",
                "- https://laravel.com/docs/12.x/requests",
                "- https://laravel.com/docs/12.x/validation",
                "- https://laravel.com/docs/12.x/migrations",
                "- https://laravel.com/docs/12.x/eloquent",
            ],
            'Inertia.js Dasar untuk Laravel' => [
                "- https://inertiajs.com/server-side-setup",
                "- https://inertiajs.com/client-side-setup",
                "- https://inertiajs.com/links",
                "- https://inertiajs.com/docs/v2/the-basics/forms",
                "- https://inertiajs.com/validation",
                "- https://inertiajs.com/shared-data",
            ],
            'Full Project: Laravel + Inertia + Vue' => [
                "- https://laravel.com/docs/12.x/routing",
                "- https://laravel.com/docs/12.x/validation",
                "- https://laravel.com/docs/12.x/eloquent",
                "- https://inertiajs.com/redirects",
                "- https://vuejs.org/api/sfc-script-setup.html",
            ],
            default => ['- Dokumentasi resmi belum dipetakan untuk track ini.'],
        };

        return match ($courseTitle) {
            default => implode("\n", array_merge([
                '- Fokus topik lesson ini:',
                $topicReference,
                '- Jalur lengkap:',
            ], $trackReferences)),
        };
    }

    /**
     * Mapping dokumentasi paling relevan berdasarkan topik.
     */
    private function buildTopicDocsReference(string $courseTitle, string $topic): string
    {
        $normalized = strtolower($topic);

        return match ($courseTitle) {
            'PHP Dasar by Project' => match (true) {
                str_contains($normalized, 'koneksi') => '- https://www.php.net/manual/en/pdo.connections.php',
                str_contains($normalized, 'select') => '- https://www.php.net/manual/en/pdo.query.php',
                str_contains($normalized, 'insert') => '- https://www.php.net/manual/en/pdostatement.execute.php',
                str_contains($normalized, 'update') => '- https://www.php.net/manual/en/pdostatement.execute.php',
                str_contains($normalized, 'delete') => '- https://www.php.net/manual/en/pdostatement.execute.php',
                str_contains($normalized, 'prepared') => '- https://www.php.net/manual/en/pdo.prepare.php',
                str_contains($normalized, 'search') => '- https://www.php.net/manual/en/pdo.prepare.php',
                str_contains($normalized, 'pagination') => '- https://www.php.net/manual/en/pdostatement.fetch.php',
                str_contains($normalized, 'crud') => '- https://www.php.net/manual/en/book.pdo.php',
                default => '- https://www.php.net/manual/en/tutorial.php',
            },
            'OOP PHP Dasar by Project' => match (true) {
                str_contains($normalized, 'class') => '- https://www.php.net/manual/en/language.oop5.basic.php',
                str_contains($normalized, 'inheritance') => '- https://www.php.net/manual/en/language.oop5.inheritance.php',
                str_contains($normalized, 'interface') => '- https://www.php.net/manual/en/language.oop5.interfaces.php',
                str_contains($normalized, 'trait') => '- https://www.php.net/manual/en/language.oop5.traits.php',
                default => '- https://www.php.net/manual/en/book.classobj.php',
            },
            'MVC Dasar (PHP Native) by Project' => match (true) {
                str_contains($normalized, 'routing') => '- https://www.php.net/manual/en/reserved.variables.server.php',
                str_contains($normalized, 'view') => '- https://www.php.net/manual/en/function.include.php',
                str_contains($normalized, 'controller') => '- https://www.php.net/manual/en/language.oop5.php',
                default => '- https://www.php.net/manual/en/tutorial.php',
            },
            'Vue 3 Dasar by Project' => match (true) {
                str_contains($normalized, 'typescript') => '- https://vuejs.org/guide/typescript/composition-api',
                str_contains($normalized, 'script setup') => '- https://vuejs.org/api/sfc-script-setup.html',
                str_contains($normalized, 'state') => '- https://vuejs.org/guide/essentials/reactivity-fundamentals',
                str_contains($normalized, 'computed') => '- https://vuejs.org/guide/essentials/computed',
                str_contains($normalized, 'event') => '- https://vuejs.org/guide/components/events',
                str_contains($normalized, 'props') => '- https://vuejs.org/guide/components/props',
                str_contains($normalized, 'emit') => '- https://vuejs.org/guide/components/events',
                str_contains($normalized, 'loop') => '- https://vuejs.org/guide/essentials/list',
                str_contains($normalized, 'conditional') => '- https://vuejs.org/guide/essentials/conditional',
                str_contains($normalized, 'form') => '- https://vuejs.org/guide/essentials/forms.html',
                str_contains($normalized, 'watch') => '- https://vuejs.org/guide/essentials/watchers.html',
                str_contains($normalized, 'localstorage') => '- https://vuejs.org/guide/essentials/watchers.html',
                str_contains($normalized, 'reusable') => '- https://vuejs.org/guide/components/registration',
                default => '- https://vuejs.org/api/sfc-script-setup.html',
            },
            'Laravel Dasar by Project' => match (true) {
                str_contains($normalized, 'routing') => '- https://laravel.com/docs/12.x/routing',
                str_contains($normalized, 'controller') => '- https://laravel.com/docs/12.x/controllers',
                str_contains($normalized, 'request') => '- https://laravel.com/docs/12.x/requests',
                str_contains($normalized, 'validasi') => '- https://laravel.com/docs/12.x/validation',
                str_contains($normalized, 'migration') => '- https://laravel.com/docs/12.x/migrations',
                str_contains($normalized, 'eloquent') => '- https://laravel.com/docs/12.x/eloquent',
                default => '- https://laravel.com/docs/12.x',
            },
            'Inertia.js Dasar untuk Laravel' => match (true) {
                str_contains($normalized, 'setup') => '- https://inertiajs.com/client-side-setup',
                str_contains($normalized, 'render') => '- https://inertiajs.com/responses',
                str_contains($normalized, 'props') => '- https://inertiajs.com/shared-data',
                str_contains($normalized, 'form') => '- https://inertiajs.com/docs/v2/the-basics/forms',
                str_contains($normalized, 'link') => '- https://inertiajs.com/links',
                default => '- https://inertiajs.com/server-side-setup',
            },
            'Full Project: Laravel + Inertia + Vue' => match (true) {
                str_contains($normalized, 'requirement') => '- https://laravel.com/docs/12.x/lifecycle',
                str_contains($normalized, 'service') => '- https://laravel.com/docs/12.x/container',
                str_contains($normalized, 'testing') => '- https://laravel.com/docs/12.x/testing',
                str_contains($normalized, 'rilis') => '- https://laravel.com/docs/12.x/deployment',
                default => '- https://laravel.com/docs/12.x',
            },
            default => '- Dokumentasi topik belum dipetakan.',
        };
    }

    /**
     * Milestone agar user tahu progres menuju bisa bikin project sendiri.
     */
    private function buildIndependenceMilestone(string $courseTitle, int $lessonNumber): string
    {
        $totalLessons = count($this->courseTopics()[$courseTitle] ?? []);
        $percentage = $totalLessons > 0 ? (int) round(($lessonNumber / $totalLessons) * 100) : 0;

        if ($lessonNumber >= $totalLessons && $totalLessons > 0) {
            return implode("\n", [
                "- Progress track: {$percentage}% (Capstone).",
                "- Target: kamu bisa membangun project mandiri tanpa copy penuh.",
                $this->buildCapstoneDeliverables($courseTitle),
            ]);
        }

        if ($percentage <= 33) {
            return "- Progress track: {$percentage}% | Fokus: paham pondasi + baca kode.";
        }

        if ($percentage <= 66) {
            return "- Progress track: {$percentage}% | Fokus: modifikasi fitur dan debug mandiri.";
        }

        return "- Progress track: {$percentage}% | Fokus: integrasi antar fitur jadi mini project utuh.";
    }

    /**
     * Deliverable akhir per track sebagai indikator siap mandiri.
     */
    private function buildCapstoneDeliverables(string $courseTitle): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project' => implode("\n", [
                "- Deliverable: aplikasi Catatan Keuangan CLI (input, validasi, ringkasan).",
                "- Bukti siap: bisa tambah fitur filter kategori tanpa panduan langkah demi langkah.",
            ]),
            'Vue 3 Dasar by Project' => implode("\n", [
                "- Deliverable: Task Tracker Vue 3 (add, complete, filter, edit).",
                "- Bukti siap: bisa pecah jadi komponen reusable + state lokal rapi.",
            ]),
            'Laravel Dasar by Project' => implode("\n", [
                "- Deliverable: backend Course + Tutorial + Challenge dengan service/request pattern.",
                "- Bukti siap: bisa tambah modul baru dengan pola yang sama.",
            ]),
            'Inertia.js Dasar untuk Laravel' => implode("\n", [
                "- Deliverable: flow page Laravel -> Inertia -> Vue tanpa REST API terpisah.",
                "- Bukti siap: bisa buat page baru + form submit + validasi sendiri.",
            ]),
            'Full Project: Laravel + Inertia + Vue' => implode("\n", [
                "- Deliverable: MVP e-learning end-to-end siap demo/deploy.",
                "- Bukti siap: bisa jelaskan arsitektur dan lanjutkan fitur berikutnya mandiri.",
            ]),
            default => "- Deliverable: mini project track selesai dan bisa di-refactor mandiri.",
        };
    }

    /**
     * Contoh source code ringkas (gaya tutorial cepat seperti W3Schools).
     */
    private function buildCodeExample(string $courseTitle, string $topic): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project' => $this->buildPhpDasarCodeExample($topic),
            'OOP PHP Dasar by Project' => $this->buildOopPhpCodeExample($topic),
            'MVC Dasar (PHP Native) by Project' => $this->buildMvcNativeCodeExample($topic),
            'Vue 3 Dasar by Project' => $this->buildVueDasarCodeExample($topic),
            'Laravel Dasar by Project' => $this->buildLaravelDasarCodeExample($topic),
            'Inertia.js Dasar untuk Laravel' => $this->buildInertiaDasarCodeExample($topic),
            'Full Project: Laravel + Inertia + Vue' => $this->buildFullProjectCodeExample($topic),
            default => $this->resolveCourseFallbackCodeExample($courseTitle, $topic),
        };
    }

    /**
     * Output hasil dari contoh kode agar gaya tutorial lebih mudah diikuti.
     */
    private function buildExpectedOutput(string $courseTitle, string $topic): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project' => $this->buildPhpDasarExpectedOutput($topic),
            'OOP PHP Dasar by Project' => $this->buildOopPhpExpectedOutput($topic),
            'MVC Dasar (PHP Native) by Project' => $this->buildMvcNativeExpectedOutput($topic),
            'Vue 3 Dasar by Project' => $this->buildVueDasarExpectedOutput($topic),
            'Laravel Dasar by Project' => $this->buildLaravelDasarExpectedOutput($topic),
            'Inertia.js Dasar untuk Laravel' => $this->buildInertiaDasarExpectedOutput($topic),
            'Full Project: Laravel + Inertia + Vue' => $this->buildFullProjectExpectedOutput($topic),
            default => $this->buildCodeBlock('txt', [
                'Program berjalan tanpa error.',
                'Topik: '.$topic,
            ]),
        };
    }

    /**
     * Contoh kode lengkap per topik - PHP Dasar.
     */
    private function buildPhpDasarCodeExample(string $topic): string
    {
        return match ($topic) {
            'Setup Project PHP + Database' => $this->buildCodeBlock('php', [
                '<?php',
                '$dsn = "mysql:host=127.0.0.1;dbname=elearning_php;charset=utf8mb4";',
                '$pdo = new PDO($dsn, "root", "");',
                '$sql = "CREATE TABLE IF NOT EXISTS transactions (',
                '    id INT AUTO_INCREMENT PRIMARY KEY,',
                '    title VARCHAR(120) NOT NULL,',
                '    type ENUM(\'masuk\', \'keluar\') NOT NULL,',
                '    amount INT NOT NULL,',
                '    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                ')";',
                '$pdo->exec($sql);',
                'echo "Database dan tabel siap";',
            ]),
            'Koneksi Database dengan PDO' => $this->buildCodeBlock('php', [
                '<?php',
                '$config = [',
                '    "host" => "127.0.0.1",',
                '    "dbname" => "elearning_php",',
                '    "username" => "root",',
                '    "password" => "",',
                '];',
                '$dsn = "mysql:host={$config["host"]};dbname={$config["dbname"]};charset=utf8mb4";',
                '$pdo = new PDO($dsn, $config["username"], $config["password"]);',
                '$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);',
                'echo "Koneksi berhasil";',
            ]),
            'Query SELECT dan Tampilkan Data' => $this->buildCodeBlock('php', [
                '<?php',
                '$stmt = $pdo->query("SELECT id, title, amount FROM transactions ORDER BY id DESC LIMIT 5");',
                '$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);',
                'foreach ($rows as $row) {',
                '    echo "#{$row["id"]} {$row["title"]} - {$row["amount"]}".PHP_EOL;',
                '}',
            ]),
            'Query INSERT Data' => $this->buildCodeBlock('php', [
                '<?php',
                '$sql = "INSERT INTO transactions (title, type, amount) VALUES (:title, :type, :amount)";',
                '$stmt = $pdo->prepare($sql);',
                '$stmt->execute([',
                '    "title" => "Beli domain",',
                '    "type" => "keluar",',
                '    "amount" => 150000,',
                ']);',
                'echo "Insert berhasil. ID baru: ".$pdo->lastInsertId();',
            ]),
            'Query UPDATE Data' => $this->buildCodeBlock('php', [
                '<?php',
                '$sql = "UPDATE transactions SET amount = :amount WHERE id = :id";',
                '$stmt = $pdo->prepare($sql);',
                '$stmt->execute([',
                '    "amount" => 175000,',
                '    "id" => 1,',
                ']);',
                'echo "Data diupdate: ".$stmt->rowCount();',
            ]),
            'Query DELETE Data' => $this->buildCodeBlock('php', [
                '<?php',
                '$stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");',
                '$stmt->execute(["id" => 3]);',
                'echo "Data terhapus: ".$stmt->rowCount();',
            ]),
            'Prepared Statement + Validasi Input' => $this->buildCodeBlock('php', [
                '<?php',
                '$title = trim($_POST["title"] ?? "");',
                '$amount = (int) ($_POST["amount"] ?? 0);',
                'if ($title === "" || $amount <= 0) {',
                '    echo "Validasi gagal";',
                '    return;',
                '}',
                '$stmt = $pdo->prepare("INSERT INTO transactions (title, type, amount) VALUES (:title, :type, :amount)");',
                '$stmt->execute([',
                '    "title" => $title,',
                '    "type" => "keluar",',
                '    "amount" => $amount,',
                ']);',
                'echo "Data valid dan tersimpan";',
            ]),
            'Fitur Search dengan LIKE' => $this->buildCodeBlock('php', [
                '<?php',
                '$keyword = "domain";',
                '$stmt = $pdo->prepare("SELECT id, title, amount FROM transactions WHERE title LIKE :keyword ORDER BY id DESC");',
                '$stmt->execute(["keyword" => "%".$keyword."%"]);',
                '$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);',
                'foreach ($rows as $row) {',
                '    echo "#{$row["id"]} {$row["title"]} - {$row["amount"]}".PHP_EOL;',
                '}',
            ]),
            'Pagination Sederhana' => $this->buildCodeBlock('php', [
                '<?php',
                '$page = max(1, (int) ($_GET["page"] ?? 1));',
                '$perPage = 5;',
                '$offset = ($page - 1) * $perPage;',
                '$stmt = $pdo->prepare("SELECT id, title, amount FROM transactions ORDER BY id DESC LIMIT :limit OFFSET :offset");',
                '$stmt->bindValue(":limit", $perPage, PDO::PARAM_INT);',
                '$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);',
                '$stmt->execute();',
                '$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);',
                'echo "Page: $page".PHP_EOL;',
                'foreach ($rows as $row) {',
                '    echo "{$row["id"]}. {$row["title"]} - {$row["amount"]}".PHP_EOL;',
                '}',
            ]),
            'Mini Project: CRUD Catatan Keuangan' => $this->buildCodeBlock('php', [
                '<?php',
                'function listTransactions(PDO $pdo): array',
                '{',
                '    return $pdo->query("SELECT id, title, type, amount FROM transactions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);',
                '}',
                '',
                '$items = listTransactions($pdo);',
                '$saldo = 0;',
                'foreach ($items as $item) {',
                '    $saldo += $item["type"] === "masuk" ? (int) $item["amount"] : -(int) $item["amount"];',
                '    echo "{$item["title"]} ({$item["type"]}) - {$item["amount"]}".PHP_EOL;',
                '}',
                'echo "Saldo akhir: ".$saldo;',
            ]),
            default => $this->resolveCourseFallbackCodeExample('PHP Dasar by Project', $topic),
        };
    }

    /**
     * Output contoh per topik - PHP Dasar.
     */
    private function buildPhpDasarExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Setup Project PHP + Database' => $this->buildCodeBlock('txt', [
                'Database dan tabel siap',
            ]),
            'Koneksi Database dengan PDO' => $this->buildCodeBlock('txt', [
                'Koneksi berhasil',
            ]),
            'Query SELECT dan Tampilkan Data' => $this->buildCodeBlock('txt', [
                '#7 Beli domain - 150000',
                '#6 Bayar internet - 350000',
            ]),
            'Query INSERT Data' => $this->buildCodeBlock('txt', [
                'Insert berhasil. ID baru: 8',
            ]),
            'Query UPDATE Data' => $this->buildCodeBlock('txt', [
                'Data diupdate: 1',
            ]),
            'Query DELETE Data' => $this->buildCodeBlock('txt', [
                'Data terhapus: 1',
            ]),
            'Prepared Statement + Validasi Input' => $this->buildCodeBlock('txt', [
                'Data valid dan tersimpan',
            ]),
            'Fitur Search dengan LIKE' => $this->buildCodeBlock('txt', [
                '#8 Beli domain - 150000',
            ]),
            'Pagination Sederhana' => $this->buildCodeBlock('txt', [
                'Page: 1',
                '8. Beli domain - 150000',
                '7. Bayar internet - 350000',
            ]),
            'Mini Project: CRUD Catatan Keuangan' => $this->buildCodeBlock('txt', [
                'Gaji Februari (masuk) - 5000000',
                'Beli domain (keluar) - 150000',
                'Saldo akhir: 4850000',
            ]),
            default => $this->buildCodeBlock('txt', [
                'Output sesuai skenario topik: '.$topic,
            ]),
        };
    }

    /**
     * Contoh kode lengkap per topik - OOP PHP Dasar.
     */
    private function buildOopPhpCodeExample(string $topic): string
    {
        return match ($topic) {
            'Pengenalan Class dan Object' => $this->buildCodeBlock('php', [
                '<?php',
                'class Course {',
                '    public string $title = "Belajar OOP";',
                '}',
                '$course = new Course();',
                'echo $course->title;',
            ]),
            'Property dan Method' => $this->buildCodeBlock('php', [
                '<?php',
                'class Product {',
                '    public string $name;',
                '    public int $stock;',
                '    public function info(): string {',
                '        return $this->name." stok: ".$this->stock;',
                '    }',
                '}',
                '$p = new Product();',
                '$p->name = "Mouse";',
                '$p->stock = 10;',
                'echo $p->info();',
            ]),
            'Constructor dan Encapsulation' => $this->buildCodeBlock('php', [
                '<?php',
                'class Product {',
                '    private int $price;',
                '    public function __construct(private string $name, int $price)',
                '    {',
                '        $this->setPrice($price);',
                '    }',
                '    public function setPrice(int $price): void {',
                '        if ($price > 0) $this->price = $price;',
                '    }',
                '    public function getPrice(): int { return $this->price; }',
                '}',
            ]),
            'Inheritance Dasar' => $this->buildCodeBlock('php', [
                '<?php',
                'class Item {',
                '    public function label(): string { return "Item"; }',
                '}',
                'class Ebook extends Item {',
                '    public function label(): string { return "Ebook"; }',
                '}',
                'echo (new Ebook())->label();',
            ]),
            'Method Overriding' => $this->buildCodeBlock('php', [
                '<?php',
                'class Item {',
                '    public function info(): string { return "Info Item"; }',
                '}',
                'class VideoCourse extends Item {',
                '    public function info(): string {',
                '        return parent::info()." - Video";',
                '    }',
                '}',
                'echo (new VideoCourse())->info();',
            ]),
            'Interface dan Implementasi' => $this->buildCodeBlock('php', [
                '<?php',
                'interface Publishable { public function publish(): string; }',
                'class Ebook implements Publishable {',
                '    public function publish(): string { return "Ebook dipublish"; }',
                '}',
                'echo (new Ebook())->publish();',
            ]),
            'Static Method dan Constant' => $this->buildCodeBlock('php', [
                '<?php',
                'class ProductHelper {',
                '    public const TAX = 0.11;',
                '    public static function rupiah(int $value): string {',
                '        return "Rp ".number_format($value, 0, ",", ".");',
                '    }',
                '}',
                'echo ProductHelper::rupiah(125000);',
            ]),
            'Studi Kasus: Service Layer Sederhana' => $this->buildCodeBlock('php', [
                '<?php',
                'class ProductService {',
                '    public function totalStockValue(array $items): int {',
                '        $total = 0;',
                '        foreach ($items as $item) {',
                '            $total += $item["price"] * $item["stock"];',
                '        }',
                '        return $total;',
                '    }',
                '}',
            ]),
            'Mini Project: Manajemen Produk OOP' => $this->buildCodeBlock('php', [
                '<?php',
                '$products = [',
                '    ["name" => "Keyboard", "price" => 250000, "stock" => 8],',
                '    ["name" => "Mouse", "price" => 150000, "stock" => 15],',
                '];',
                '$service = new ProductService();',
                'echo "Total nilai stok: ".$service->totalStockValue($products);',
            ]),
            default => $this->resolveCourseFallbackCodeExample('OOP PHP Dasar by Project', $topic),
        };
    }

    /**
     * Output contoh per topik - OOP PHP Dasar.
     */
    private function buildOopPhpExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Pengenalan Class dan Object' => $this->buildCodeBlock('txt', ['Belajar OOP']),
            'Property dan Method' => $this->buildCodeBlock('txt', ['Mouse stok: 10']),
            'Constructor dan Encapsulation' => $this->buildCodeBlock('txt', ['Harga produk tersimpan dan tervalidasi']),
            'Inheritance Dasar' => $this->buildCodeBlock('txt', ['Ebook']),
            'Method Overriding' => $this->buildCodeBlock('txt', ['Info Item - Video']),
            'Interface dan Implementasi' => $this->buildCodeBlock('txt', ['Ebook dipublish']),
            'Static Method dan Constant' => $this->buildCodeBlock('txt', ['Rp 125.000']),
            'Studi Kasus: Service Layer Sederhana' => $this->buildCodeBlock('txt', ['Total nilai stok berhasil dihitung']),
            'Mini Project: Manajemen Produk OOP' => $this->buildCodeBlock('txt', ['Total nilai stok: 4250000']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Contoh kode lengkap per topik - MVC Dasar Native PHP.
     */
    private function buildMvcNativeCodeExample(string $topic): string
    {
        return match ($topic) {
            'Konsep MVC dan Alur Request' => $this->buildCodeBlock('php', [
                '<?php',
                '// public/index.php',
                '$page = $_GET["page"] ?? "home";',
                'require "../app/controllers/HomeController.php";',
                '$controller = new HomeController();',
                'echo $controller->index($page);',
            ]),
            'Routing Sederhana Native PHP' => $this->buildCodeBlock('php', [
                '<?php',
                '$route = $_GET["page"] ?? "home";',
                'switch ($route) {',
                '    case "posts": require "../app/controllers/PostController.php"; break;',
                '    case "about": require "../app/controllers/AboutController.php"; break;',
                '    default: require "../app/controllers/HomeController.php";',
                '}',
            ]),
            'Controller untuk Logika Bisnis' => $this->buildCodeBlock('php', [
                '<?php',
                'class PostController {',
                '    public function index(): string {',
                '        $posts = [["title" => "Post 1"], ["title" => "Post 2"]];',
                '        ob_start();',
                '        include "../app/views/posts/index.php";',
                '        return ob_get_clean();',
                '    }',
                '}',
            ]),
            'Model untuk Data' => $this->buildCodeBlock('php', [
                '<?php',
                'class PostModel {',
                '    public function all(): array {',
                '        return [',
                '            ["id" => 1, "title" => "Belajar MVC"],',
                '            ["id" => 2, "title" => "Belajar Router"],',
                '        ];',
                '    }',
                '}',
            ]),
            'View untuk Tampilan' => $this->buildCodeBlock('php', [
                '<?php foreach ($posts as $post): ?>',
                '  <article>',
                '    <h3><?= htmlspecialchars($post["title"]) ?></h3>',
                '  </article>',
                '<?php endforeach; ?>',
            ]),
            'Integrasi MVC End to End' => $this->buildCodeBlock('php', [
                '<?php',
                '$model = new PostModel();',
                '$posts = $model->all();',
                '$controller = new PostController();',
                'echo $controller->renderList($posts);',
            ]),
            'Refactor Struktur Folder' => $this->buildCodeBlock('txt', [
                'app/',
                '  controllers/',
                '  models/',
                '  views/',
                'core/',
                'public/index.php',
            ]),
            'Mini Project: Blog MVC Sederhana' => $this->buildCodeBlock('php', [
                '<?php',
                '// Route: /posts/show?id=1',
                '$id = (int) ($_GET["id"] ?? 0);',
                '$model = new PostModel();',
                '$post = $model->findById($id);',
                'if ($post === null) { echo "404 Not Found"; return; }',
                'echo "<h1>{$post["title"]}</h1>";',
            ]),
            default => $this->resolveCourseFallbackCodeExample('MVC Dasar (PHP Native) by Project', $topic),
        };
    }

    /**
     * Output contoh per topik - MVC Native.
     */
    private function buildMvcNativeExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Konsep MVC dan Alur Request' => $this->buildCodeBlock('txt', ['Halaman home berhasil dirender dari controller']),
            'Routing Sederhana Native PHP' => $this->buildCodeBlock('txt', ['Route /posts diarahkan ke PostController']),
            'Controller untuk Logika Bisnis' => $this->buildCodeBlock('txt', ['Daftar post tampil dari controller']),
            'Model untuk Data' => $this->buildCodeBlock('txt', ['Data post berhasil diambil dari model']),
            'View untuk Tampilan' => $this->buildCodeBlock('txt', ['Judul post tampil di browser']),
            'Integrasi MVC End to End' => $this->buildCodeBlock('txt', ['Alur router -> controller -> model -> view berjalan']),
            'Refactor Struktur Folder' => $this->buildCodeBlock('txt', ['Struktur project lebih rapi dan mudah dikembangkan']),
            'Mini Project: Blog MVC Sederhana' => $this->buildCodeBlock('txt', ['Halaman list, detail, dan 404 siap digunakan']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Contoh kode lengkap per topik - Vue 3 Dasar.
     */
    private function buildVueDasarCodeExample(string $topic): string
    {
        return match ($topic) {
            'Setup Vue 3 + TypeScript' => $this->buildCodeBlock('bash', [
                'npm create vue@latest vue-task-tracker',
                'cd vue-task-tracker',
                'npm install',
                'npm run dev',
            ]),
            'Komponen Dasar dengan script setup' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { ref } from "vue";',
                'const appName = ref("Task Tracker Vue");',
                '</script>',
                '',
                '<template>',
                '  <h1>{{ appName }}</h1>',
                '  <p>Belajar by project</p>',
                '</template>',
            ]),
            'Props dan Emit Antar Komponen' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'type TaskItem = { id: number; title: string; done: boolean };',
                'const props = defineProps<{ task: TaskItem }>();',
                'const emit = defineEmits<{ (e: "toggle", id: number): void }>();',
                '</script>',
                '',
                '<template>',
                '  <button @click="emit(\'toggle\', props.task.id)">',
                '    {{ props.task.done ? "Undo" : "Done" }} - {{ props.task.title }}',
                '  </button>',
                '</template>',
            ]),
            'Reactive State dan Event Handling' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { ref } from "vue";',
                'const count = ref(0);',
                'const increment = () => count.value++;',
                'const decrement = () => count.value = Math.max(0, count.value - 1);',
                '</script>',
                '',
                '<template>',
                '  <button @click="decrement">-</button>',
                '  <span>{{ count }}</span>',
                '  <button @click="increment">+</button>',
                '</template>',
            ]),
            'Loop dan Conditional Rendering' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'const tasks = [',
                '  { id: 1, title: "Belajar props", done: true },',
                '  { id: 2, title: "Belajar emit", done: false },',
                '];',
                '</script>',
                '',
                '<template>',
                '  <p v-if="tasks.length === 0">Task kosong</p>',
                '  <ul v-else>',
                '    <li v-for="task in tasks" :key="task.id">',
                '      {{ task.title }} - {{ task.done ? "Selesai" : "Belum" }}',
                '    </li>',
                '  </ul>',
                '</template>',
            ]),
            'Form Input dengan v-model' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { ref } from "vue";',
                'const title = ref("");',
                'const error = ref("");',
                'const submit = () => {',
                '  error.value = title.value.trim() ? "" : "Judul task wajib diisi";',
                '};',
                '</script>',
                '',
                '<template>',
                '  <input v-model="title" placeholder="Tulis task..." />',
                '  <button @click="submit">Simpan</button>',
                '  <p v-if="error">{{ error }}</p>',
                '</template>',
            ]),
            'Computed untuk Filter Data' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { computed, ref } from "vue";',
                'const showDoneOnly = ref(false);',
                'const tasks = ref([',
                '  { id: 1, title: "Belajar Vue", done: true },',
                '  { id: 2, title: "Belajar Inertia", done: false },',
                ']);',
                'const filteredTasks = computed(() =>',
                '  showDoneOnly.value ? tasks.value.filter((task) => task.done) : tasks.value',
                ');',
                '</script>',
                '',
                '<template>',
                '  <label><input type="checkbox" v-model="showDoneOnly" /> Selesai saja</label>',
                '  <li v-for="task in filteredTasks" :key="task.id">{{ task.title }}</li>',
                '</template>',
            ]),
            'Simpan Data ke LocalStorage' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { ref, watch } from "vue";',
                'const tasks = ref<string[]>(JSON.parse(localStorage.getItem("tasks") ?? "[]"));',
                'watch(tasks, (value) => {',
                '  localStorage.setItem("tasks", JSON.stringify(value));',
                '}, { deep: true });',
                '</script>',
                '',
                '<template><p>Data task otomatis tersimpan di browser</p></template>',
            ]),
            'Refactor ke Komponen Reusable' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'type TaskItem = { id: number; title: string; done: boolean };',
                'defineProps<{ task: TaskItem }>();',
                '</script>',
                '',
                '<template>',
                '  <article class="task-card">',
                '    <p>{{ task.title }}</p>',
                '  </article>',
                '</template>',
            ]),
            'Mini Project: Task Tracker Vue' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { ref } from "vue";',
                'type TaskItem = { id: number; title: string; done: boolean };',
                'const tasks = ref<TaskItem[]>([]);',
                'const input = ref("");',
                'const addTask = () => {',
                '  const title = input.value.trim();',
                '  if (!title) return;',
                '  tasks.value.push({ id: Date.now(), title, done: false });',
                '  input.value = "";',
                '};',
                '</script>',
                '',
                '<template>',
                '  <input v-model="input" placeholder="Task baru" />',
                '  <button @click="addTask">Tambah</button>',
                '  <li v-for="task in tasks" :key="task.id">{{ task.title }}</li>',
                '</template>',
            ]),
            default => $this->resolveCourseFallbackCodeExample('Vue 3 Dasar by Project', $topic),
        };
    }

    /**
     * Output contoh per topik - Vue 3 Dasar.
     */
    private function buildVueDasarExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Setup Vue 3 + TypeScript' => $this->buildCodeBlock('txt', ['Project Vue berjalan di localhost']),
            'Komponen Dasar dengan script setup' => $this->buildCodeBlock('txt', ['Task Tracker Vue', 'Belajar by project']),
            'Props dan Emit Antar Komponen' => $this->buildCodeBlock('txt', ['Klik tombol child memicu event toggle ke parent']),
            'Reactive State dan Event Handling' => $this->buildCodeBlock('txt', ['0', '1', '2', '1']),
            'Loop dan Conditional Rendering' => $this->buildCodeBlock('txt', ['Belajar props - Selesai', 'Belajar emit - Belum']),
            'Form Input dengan v-model' => $this->buildCodeBlock('txt', ['Judul task wajib diisi (jika kosong)']),
            'Computed untuk Filter Data' => $this->buildCodeBlock('txt', ['Saat checkbox aktif, hanya task done yang tampil']),
            'Simpan Data ke LocalStorage' => $this->buildCodeBlock('txt', ['Refresh browser, data task tetap ada']),
            'Refactor ke Komponen Reusable' => $this->buildCodeBlock('txt', ['Task card dipakai ulang di halaman list']),
            'Mini Project: Task Tracker Vue' => $this->buildCodeBlock('txt', ['Task baru muncul di list setelah klik Tambah']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Contoh kode lengkap per topik - Laravel Dasar.
     */
    private function buildLaravelDasarCodeExample(string $topic): string
    {
        return match ($topic) {
            'Struktur Dasar Laravel' => $this->buildCodeBlock('txt', [
                'app/',
                'routes/',
                'resources/',
                'database/',
                'public/',
            ]),
            'Routing dan Controller' => $this->buildCodeBlock('php', [
                '// routes/web.php',
                'Route::get("/courses", [CourseController::class, "index"])->name("courses.index");',
                '',
                '// app/Http/Controllers/CourseController.php',
                'public function index() {',
                '    return "Daftar course";',
                '}',
            ]),
            'Blade dan View Data' => $this->buildCodeBlock('php', [
                '// Controller',
                'return view("courses.index", ["title" => "Daftar Course"]);',
                '',
                '// resources/views/courses/index.blade.php',
                '<h1>{{ $title }}</h1>',
            ]),
            'Migration dan Seeder' => $this->buildCodeBlock('php', [
                '// migration',
                'Schema::create("courses", function (Blueprint $table) {',
                '    $table->id();',
                '    $table->string("title");',
                '    $table->timestamps();',
                '});',
            ]),
            'Model dan Eloquent Dasar' => $this->buildCodeBlock('php', [
                'class Course extends Model {',
                '    protected $fillable = ["title", "level", "total_lessons"];',
                '}',
                '',
                '$courses = Course::query()->orderBy("id")->get();',
            ]),
            'Form Request Validation' => $this->buildCodeBlock('php', [
                'class StoreCourseRequest extends FormRequest {',
                '    public function rules(): array {',
                '        return [',
                '            "title" => ["required", "string", "max:255"],',
                '            "total_lessons" => ["required", "integer", "min:1"],',
                '        ];',
                '    }',
                '}',
            ]),
            'Service Layer untuk Logic' => $this->buildCodeBlock('php', [
                'class CourseService {',
                '    public function getCourseList(): Collection {',
                '        return Course::query()->orderBy("id")->get();',
                '    }',
                '}',
            ]),
            'Relasi Database Dasar' => $this->buildCodeBlock('php', [
                'class Course extends Model {',
                '    public function tutorials() { return $this->hasMany(CourseTutorial::class); }',
                '}',
                '',
                'class CourseTutorial extends Model {',
                '    public function course() { return $this->belongsTo(Course::class); }',
                '}',
            ]),
            'Pagination dan Query Filter' => $this->buildCodeBlock('php', [
                '$query = Course::query();',
                'if ($search = request("search")) {',
                '    $query->where("title", "like", "%".$search."%");',
                '}',
                '$courses = $query->paginate(10)->withQueryString();',
            ]),
            'Error Handling Dasar' => $this->buildCodeBlock('php', [
                'public function show(int $id) {',
                '    $course = Course::query()->findOrFail($id);',
                '    return view("courses.show", compact("course"));',
                '}',
            ]),
            'Mini Project: Backend Kelas Online' => $this->buildCodeBlock('php', [
                'Route::get("/courses", [CourseController::class, "index"]);',
                'Route::get("/courses/{id}", [CourseController::class, "show"]);',
                'Route::get("/courses/{id}/tutorials", [CourseTutorialController::class, "index"]);',
                'Route::get("/courses/{id}/challenges", [CourseChallengeController::class, "index"]);',
            ]),
            default => $this->resolveCourseFallbackCodeExample('Laravel Dasar by Project', $topic),
        };
    }

    /**
     * Output contoh per topik - Laravel Dasar.
     */
    private function buildLaravelDasarExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Struktur Dasar Laravel' => $this->buildCodeBlock('txt', ['Struktur project Laravel siap digunakan']),
            'Routing dan Controller' => $this->buildCodeBlock('txt', ['Daftar course']),
            'Blade dan View Data' => $this->buildCodeBlock('txt', ['Daftar Course']),
            'Migration dan Seeder' => $this->buildCodeBlock('txt', ['Tabel courses berhasil dibuat']),
            'Model dan Eloquent Dasar' => $this->buildCodeBlock('txt', ['Data course berhasil diambil']),
            'Form Request Validation' => $this->buildCodeBlock('txt', ['Validasi request berjalan']),
            'Service Layer untuk Logic' => $this->buildCodeBlock('txt', ['Controller lebih tipis, logic pindah ke service']),
            'Relasi Database Dasar' => $this->buildCodeBlock('txt', ['Relasi course -> tutorials aktif']),
            'Pagination dan Query Filter' => $this->buildCodeBlock('txt', ['Halaman 1 dari data course tampil']),
            'Error Handling Dasar' => $this->buildCodeBlock('txt', ['404 saat id course tidak ditemukan']),
            'Mini Project: Backend Kelas Online' => $this->buildCodeBlock('txt', ['Flow course/tutorial/challenge siap digunakan']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Contoh kode lengkap per topik - Inertia Dasar.
     */
    private function buildInertiaDasarCodeExample(string $topic): string
    {
        return match ($topic) {
            'Konsep Laravel + Inertia' => $this->buildCodeBlock('php', [
                'Route::get("/courses", [CourseController::class, "index"]);',
                '',
                'public function index() {',
                '    return Inertia::render("Courses");',
                '}',
            ]),
            'Inertia Render dari Controller' => $this->buildCodeBlock('php', [
                'return Inertia::render("Courses", [',
                '    "title" => "Inertia Basic",',
                '    "courses" => $courses,',
                ']);',
            ]),
            'Props dari Laravel ke Vue' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'defineProps<{ title: string; courses: Array<{ id: number; title: string }> }>();',
                '</script>',
                '',
                '<template><h1>{{ title }}</h1></template>',
            ]),
            'Layout dan Shared Components' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import AppLayout from "../Layouts/AppLayout.vue";',
                '</script>',
                '',
                '<template>',
                '  <AppLayout><h2>Courses</h2></AppLayout>',
                '</template>',
            ]),
            'Form Submission Inertia' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { useForm } from "@inertiajs/vue3";',
                'const form = useForm({ answer: "" });',
                '</script>',
                '',
                '<template><form @submit.prevent="form.post(\'/submit\')"></form></template>',
            ]),
            'Flash Message dan Error Handling' => $this->buildCodeBlock('php', [
                '// HandleInertiaRequests',
                '"flash" => [',
                '    "success" => fn () => session("success"),',
                '],',
            ]),
            'Navigasi Antar Page Inertia' => $this->buildCodeBlock('vue', [
                '<script setup lang="ts">',
                'import { Link } from "@inertiajs/vue3";',
                '</script>',
                '<template><Link href="/courses">Courses</Link></template>',
            ]),
            'Mini Project: Course List Interaktif' => $this->buildCodeBlock('php', [
                'return Inertia::render("Courses", [',
                '    "courses" => $courses,',
                '    "levels" => $levels,',
                '    "courseCount" => $courseCount,',
                ']);',
            ]),
            default => $this->resolveCourseFallbackCodeExample('Inertia.js Dasar untuk Laravel', $topic),
        };
    }

    /**
     * Output contoh per topik - Inertia Dasar.
     */
    private function buildInertiaDasarExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Konsep Laravel + Inertia' => $this->buildCodeBlock('txt', ['Halaman Vue dirender via Laravel controller']),
            'Inertia Render dari Controller' => $this->buildCodeBlock('txt', ['Page Courses menerima props']),
            'Props dari Laravel ke Vue' => $this->buildCodeBlock('txt', ['Title dan list courses tampil di page']),
            'Layout dan Shared Components' => $this->buildCodeBlock('txt', ['Header/layout konsisten di semua page']),
            'Form Submission Inertia' => $this->buildCodeBlock('txt', ['Form submit tanpa API terpisah']),
            'Flash Message dan Error Handling' => $this->buildCodeBlock('txt', ['Pesan sukses/gagal tampil setelah aksi']),
            'Navigasi Antar Page Inertia' => $this->buildCodeBlock('txt', ['Navigasi antar halaman terasa SPA']),
            'Mini Project: Course List Interaktif' => $this->buildCodeBlock('txt', ['List course interaktif berjalan end-to-end']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Contoh kode lengkap per topik - Full Project.
     */
    private function buildFullProjectCodeExample(string $topic): string
    {
        return match ($topic) {
            'Menyusun Requirement Product' => $this->buildCodeBlock('md', [
                '# MVP Requirement',
                '- User dapat melihat daftar course',
                '- User dapat membuka tutorial detail',
                '- User dapat submit challenge',
            ]),
            'Mendesain Struktur Data' => $this->buildCodeBlock('sql', [
                'CREATE TABLE courses (...);',
                'CREATE TABLE course_tutorials (...);',
                'CREATE TABLE course_challenges (...);',
            ]),
            'Membuat Modul Course' => $this->buildCodeBlock('php', [
                'Route::get("/courses", [CourseController::class, "index"]);',
                'Route::get("/courses/{id}", [CourseController::class, "show"]);',
            ]),
            'Membuat Modul Tutorial' => $this->buildCodeBlock('php', [
                'Route::get("/courses/{id}/tutorials", [CourseTutorialController::class, "index"]);',
                'Route::get("/courses/{id}/tutorials/{tutorialId}", [CourseTutorialController::class, "show"]);',
            ]),
            'Membuat Modul Challenge' => $this->buildCodeBlock('php', [
                'Route::get("/courses/{id}/challenges", [CourseChallengeController::class, "index"]);',
                'Route::post("/courses/{id}/challenges/{challengeId}/submit", [CourseChallengeController::class, "store"]);',
            ]),
            'Menyusun Service dan Request Layer' => $this->buildCodeBlock('php', [
                'public function __construct(private readonly CourseService $courseService) {}',
                '$courseId = (int) $request->validated("id");',
                '$course = $this->courseService->getCourseById($courseId);',
            ]),
            'Membangun UI Course List' => $this->buildCodeBlock('vue', [
                '<article v-for="course in courses" :key="course.id">',
                '  <h3>{{ course.title }}</h3>',
                '  <p>{{ course.description }}</p>',
                '</article>',
            ]),
            'Membangun UI Course Detail' => $this->buildCodeBlock('vue', [
                '<h2>{{ course.title }}</h2>',
                '<p>{{ course.description }}</p>',
                '<Link :href="`/courses/${course.id}/tutorials`">Mulai Tutorial</Link>',
            ]),
            'Membangun UI Tutorial Detail' => $this->buildCodeBlock('vue', [
                '<h2>{{ tutorial.title }}</h2>',
                '<pre><code>{{ tutorial.content }}</code></pre>',
                '<Link :href="`/courses/${course.id}/tutorials/${nextTutorial?.id}`">Next</Link>',
            ]),
            'Membangun UI Challenge Detail' => $this->buildCodeBlock('vue', [
                '<form @submit.prevent="submitChallenge">',
                '  <textarea v-model="answer" />',
                '  <button type="submit">Submit</button>',
                '</form>',
            ]),
            'Integrasi Alur Tutorial ke Challenge' => $this->buildCodeBlock('vue', [
                '<Link :href="`/courses/${course.id}/challenges`">',
                '  Lanjut ke Challenge',
                '</Link>',
            ]),
            'Refactor dan Cleanup Project' => $this->buildCodeBlock('txt', [
                'app/Services/*',
                'app/Http/Requests/*',
                'resources/js/Pages/*',
            ]),
            'Testing Manual Skenario Utama' => $this->buildCodeBlock('md', [
                '- [ ] Buka list course',
                '- [ ] Buka detail tutorial',
                '- [ ] Submit challenge',
                '- [ ] Cek progress tersimpan',
            ]),
            'Rilis Versi MVP' => $this->buildCodeBlock('bash', [
                'php artisan migrate --seed',
                'npm run build',
                'php artisan config:cache',
            ]),
            default => $this->resolveCourseFallbackCodeExample('Full Project: Laravel + Inertia + Vue', $topic),
        };
    }

    /**
     * Output contoh per topik - Full Project.
     */
    private function buildFullProjectExpectedOutput(string $topic): string
    {
        return match ($topic) {
            'Menyusun Requirement Product' => $this->buildCodeBlock('txt', ['Dokumen requirement MVP siap']),
            'Mendesain Struktur Data' => $this->buildCodeBlock('txt', ['Skema tabel utama disetujui']),
            'Membuat Modul Course' => $this->buildCodeBlock('txt', ['List dan detail course berjalan']),
            'Membuat Modul Tutorial' => $this->buildCodeBlock('txt', ['List dan detail tutorial berjalan']),
            'Membuat Modul Challenge' => $this->buildCodeBlock('txt', ['Submit challenge berhasil']),
            'Menyusun Service dan Request Layer' => $this->buildCodeBlock('txt', ['Controller lebih rapi dan validasi terpusat']),
            'Membangun UI Course List' => $this->buildCodeBlock('txt', ['Halaman course list tampil SaaS-like']),
            'Membangun UI Course Detail' => $this->buildCodeBlock('txt', ['Detail course menampilkan progress']),
            'Membangun UI Tutorial Detail' => $this->buildCodeBlock('txt', ['Materi + code block tampil jelas']),
            'Membangun UI Challenge Detail' => $this->buildCodeBlock('txt', ['Form challenge interaktif berfungsi']),
            'Integrasi Alur Tutorial ke Challenge' => $this->buildCodeBlock('txt', ['User dapat lanjut dari tutorial ke challenge']),
            'Refactor dan Cleanup Project' => $this->buildCodeBlock('txt', ['Struktur project lebih maintainable']),
            'Testing Manual Skenario Utama' => $this->buildCodeBlock('txt', ['Semua skenario utama lulus']),
            'Rilis Versi MVP' => $this->buildCodeBlock('txt', ['MVP siap deploy']),
            default => $this->buildCodeBlock('txt', ['Output sesuai topik: '.$topic]),
        };
    }

    /**
     * Fallback code jika topik belum punya contoh spesifik.
     */
    private function resolveCourseFallbackCodeExample(string $courseTitle, string $topic): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project',
            'OOP PHP Dasar by Project',
            'MVC Dasar (PHP Native) by Project',
            'Laravel Dasar by Project',
            'Inertia.js Dasar untuk Laravel' => $this->buildCodeBlock('php', [
                '<?php',
                '// Topik: '.$topic,
                'echo "Belajar ".$topic;',
            ]),
            'Vue 3 Dasar by Project' => $this->buildCodeBlock('vue', [
                '<template><h2>Belajar '.$topic.'</h2></template>',
            ]),
            'Full Project: Laravel + Inertia + Vue' => $this->buildCodeBlock('ts', [
                'const step = "'.$topic.'";',
                'console.log("Implement step:", step);',
            ]),
            default => $this->buildCodeBlock('txt', [
                'Contoh kode belum tersedia untuk course ini.',
            ]),
        };
    }

    /**
     * Helper untuk membungkus array baris kode menjadi fenced code block.
     *
     * @param array<int, string> $lines
     */
    private function buildCodeBlock(string $language, array $lines): string
    {
        return implode("\n", [
            '```'.$language,
            ...$lines,
            '```',
        ]);
    }

    /**
     * Bagian implementasi agar setiap lesson tetap by-project.
     */
    private function buildProjectImplementation(string $courseTitle, int $lessonNumber, string $topic): string
    {
        $module = $this->resolveProjectModule($courseTitle);

        return implode("\n", [
            "Implementasi Project (By Project):",
            "- Modul target: {$module}",
            "- Target lesson {$lessonNumber}: terapkan konsep \"{$topic}\" ke modul.",
            "",
            "Task Implementasi:",
            "1. Ubah atau buat 1 bagian kode yang relevan dengan lesson ini.",
            "2. Jalankan aplikasi/skrip dan pastikan output sesuai.",
            "3. Simpan progres lesson + catatan kendala.",
            "",
            "Checklist:",
            "- [ ] Kode berjalan tanpa error.",
            "- [ ] Fitur sesuai tujuan lesson.",
            "- [ ] Siap lanjut ke lesson berikutnya.",
        ]);
    }

    /**
     * Nama modul project utama per jalur belajar.
     */
    private function resolveProjectModule(string $courseTitle): string
    {
        return match ($courseTitle) {
            'PHP Dasar by Project' => 'Catatan Keuangan CLI',
            'OOP PHP Dasar by Project' => 'Manajemen Inventori Produk',
            'MVC Dasar (PHP Native) by Project' => 'Blog MVC Native',
            'Vue 3 Dasar by Project' => 'Task Tracker Interaktif',
            'Laravel Dasar by Project' => 'Backend Kelas Online',
            'Inertia.js Dasar untuk Laravel' => 'Course Platform (Inertia UI)',
            'Full Project: Laravel + Inertia + Vue' => 'E-Learning MVP End-to-End',
            default => 'Project Belajar',
        };
    }

    /**
     * Ambil konten materi spesifik per course + lesson.
     * Return null jika lesson belum punya konten custom.
     */
    private function resolveSpecificLessonContent(string $courseTitle, int $lessonNumber): ?string
    {
        $contents = $this->courseLessonContents()[$courseTitle] ?? [];

        return $contents[$lessonNumber] ?? null;
    }

    /**
     * Materi detail bertahap.
     * Tahap ini fokus dulu pada course dasar agar pola konten jelas.
     *
     * @return array<string, array<int, string>>
     */
    private function courseLessonContents(): array
    {
        return [
            'PHP Dasar by Project' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyiapkan environment PHP di lokal.",
                    "- Menjalankan file PHP pertama dari terminal.",
                    "",
                    "Materi Inti:",
                    "- Cek instalasi dengan perintah: php -v",
                    "- Jalankan file dengan perintah: php index.php",
                    "- Pahami bahwa PHP bisa dijalankan tanpa framework.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat folder project bernama belajar-php-project.",
                    "2. Buat file index.php lalu isi output teks sederhana.",
                    "3. Jalankan file lewat terminal dan cek hasilnya.",
                    "",
                    "Latihan Singkat:",
                    "- Ubah output jadi biodata sederhana (nama, kota, tujuan belajar).",
                    "- Simpan screenshot terminal sebagai bukti berhasil.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami variabel, tipe data string, integer, dan boolean.",
                    "- Menampilkan data variabel ke output.",
                    "",
                    "Materi Inti:",
                    "- Variabel ditulis dengan awalan tanda dolar.",
                    '- Contoh: $nama = "Budi"; $umur = 21; $aktif = true;',
                    "- Gunakan echo untuk menampilkan nilai variabel.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file lesson2.php.",
                    '2. Deklarasikan variabel $nama, $umur, $kelas.',
                    "3. Tampilkan semua variabel dalam format kalimat utuh.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah variabel baru: targetBelajar.",
                    "- Tampilkan kalimat: Saya [nama] ingin menguasai [targetBelajar].",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami operator aritmatika dan perbandingan.",
                    "- Mengambil input user sederhana dari terminal.",
                    "",
                    "Materi Inti:",
                    "- Operator dasar: +, -, *, /, %",
                    "- Operator perbandingan: ==, !=, >, <, >=, <=",
                    "- Input terminal bisa memakai readline().",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file kalkulator.php.",
                    "2. Ambil 2 angka dari input user.",
                    "3. Tampilkan hasil tambah dan hasil kurang.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan validasi sederhana agar input tidak kosong.",
                    "- Tambahkan output status: angka pertama lebih besar atau tidak.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami percabangan if, elseif, dan else.",
                    "- Menentukan alur program berdasarkan kondisi.",
                    "",
                    "Materi Inti:",
                    "- Gunakan if untuk kondisi utama.",
                    "- Gunakan elseif untuk kondisi tambahan.",
                    "- Gunakan else sebagai fallback ketika kondisi lain tidak terpenuhi.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file penilaian.php.",
                    "2. Simulasikan nilai ujian dalam variabel.",
                    "3. Buat output: Lulus, Remedial, atau Tidak Lulus berdasarkan nilai.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan kategori nilai A, B, C, D.",
                    "- Uji dengan minimal 3 nilai berbeda.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami perulangan for dan while.",
                    "- Menggunakan loop untuk menghindari penulisan kode berulang.",
                    "",
                    "Materi Inti:",
                    "- for cocok saat jumlah pengulangan sudah diketahui.",
                    "- while cocok saat pengulangan tergantung kondisi.",
                    "- Hati-hati infinite loop, pastikan kondisi berhenti jelas.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file daftar-tugas.php.",
                    "2. Cetak daftar tugas harian 1 sampai 5 dengan for.",
                    "3. Buat countdown 5 ke 1 dengan while.",
                    "",
                    "Latihan Singkat:",
                    "- Cetak bilangan genap dari 2 sampai 20.",
                    "- Hitung total jumlah angka 1 sampai 10.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami function untuk membuat kode modular.",
                    "- Menggunakan parameter dan nilai balik (return).",
                    "",
                    "Materi Inti:",
                    "- Function membantu kode lebih rapi dan mudah dipakai ulang.",
                    "- Parameter adalah input function.",
                    "- return digunakan untuk mengembalikan hasil proses.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file helper.php.",
                    "2. Buat function sapaUser(nama) untuk menampilkan salam.",
                    "3. Buat function hitungDiskon(harga, persen) yang mengembalikan harga akhir.",
                    "",
                    "Latihan Singkat:",
                    "- Buat function untuk mengecek bilangan ganjil/genap.",
                    "- Panggil function tersebut untuk 3 angka berbeda.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami array sebagai struktur data kumpulan nilai.",
                    "- Melakukan perulangan data array untuk ditampilkan.",
                    "",
                    "Materi Inti:",
                    "- Array indexed cocok untuk daftar sederhana.",
                    "- Array associative cocok untuk data berpasangan key-value.",
                    "- Gunakan foreach untuk membaca isi array dengan rapi.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file daftar-pengeluaran.php.",
                    "2. Simpan 5 data pengeluaran dalam array.",
                    "3. Tampilkan semua item pengeluaran dengan foreach.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan total seluruh pengeluaran.",
                    "- Tampilkan pengeluaran paling besar.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami alur data form dengan method POST.",
                    "- Mengambil input form menggunakan superglobal PHP.",
                    "",
                    "Materi Inti:",
                    "- Method POST digunakan untuk mengirim data form.",
                    "- Data bisa diambil dari \$_POST['nama_field'].",
                    "- Selalu cek apakah request memang dikirim (isset / empty).",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file form-transaksi.php berisi form input nama dan nominal.",
                    "2. Saat submit, tampilkan kembali data yang dikirim.",
                    "3. Simpan hasil submit ke array sementara untuk simulasi data transaksi.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan field kategori transaksi.",
                    "- Tampilkan ringkasan transaksi setelah form disubmit.",
                ]),
                9 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menerapkan validasi input manual dasar di PHP.",
                    "- Mencegah data kosong atau tidak sesuai format.",
                    "",
                    "Materi Inti:",
                    "- Validasi manual dilakukan sebelum data diproses.",
                    "- Gunakan trim() untuk membersihkan input.",
                    "- Simpan error dalam array agar mudah ditampilkan ke user.",
                    "",
                    "Langkah Praktik:",
                    "1. Lanjutkan file form-transaksi.php.",
                    "2. Validasi: nama wajib diisi, nominal harus angka > 0.",
                    "3. Jika invalid, tampilkan pesan error; jika valid, simpan data.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan validasi panjang minimal untuk nama transaksi.",
                    "- Buat pesan error yang jelas dan mudah dipahami.",
                ]),
                10 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menggabungkan materi PHP dasar menjadi mini project utuh.",
                    "- Menyusun alur input, validasi, dan tampilan ringkasan data.",
                    "",
                    "Materi Inti:",
                    "- Mini project: Catatan Keuangan Sederhana.",
                    "- Fitur utama: tambah transaksi, validasi, dan hitung total.",
                    "- Struktur saran: index.php, functions.php, dan data.php (simulasi).",
                    "",
                    "Langkah Praktik:",
                    "1. Buat halaman form untuk input pemasukan/pengeluaran.",
                    "2. Validasi semua input sebelum disimpan.",
                    "3. Tampilkan daftar transaksi + total saldo akhir.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan fitur filter transaksi berdasarkan kategori.",
                    "- Tambahkan informasi ringkas: total pemasukan dan total pengeluaran.",
                ]),
            ],
            'OOP PHP Dasar by Project' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami konsep class dan object dalam OOP PHP.",
                    "- Mampu membuat object dari class sederhana.",
                    "",
                    "Materi Inti:",
                    "- Class adalah blueprint/cetakan dari sebuah object.",
                    "- Object adalah hasil instansiasi dari class.",
                    "- OOP membantu kode lebih terstruktur dan mudah dikembangkan.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file product.php.",
                    "2. Buat class Product dengan informasi nama produk dan harga.",
                    "3. Buat 2 object product dan tampilkan datanya.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan object ketiga dengan data berbeda.",
                    "- Tampilkan semua object dalam format list.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami penggunaan property dan method pada class.",
                    "- Memisahkan data (property) dan perilaku (method).",
                    "",
                    "Materi Inti:",
                    "- Property menyimpan data milik object.",
                    "- Method menyimpan aksi atau proses milik object.",
                    "- Object berbeda bisa punya nilai property berbeda dengan method yang sama.",
                    "",
                    "Langkah Praktik:",
                    "1. Lanjutkan class Product di file product.php.",
                    "2. Tambahkan property kategori dan stok.",
                    "3. Tambahkan method tampilkanInfo() untuk menampilkan ringkasan produk.",
                    "",
                    "Latihan Singkat:",
                    "- Buat method ubahStok(jumlah) untuk update stok.",
                    "- Panggil method tersebut lalu tampilkan hasil update stok.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami constructor untuk inisialisasi object.",
                    "- Memahami encapsulation dasar dengan modifier access.",
                    "",
                    "Materi Inti:",
                    "- Constructor (__construct) dipanggil saat object dibuat.",
                    "- Property private tidak bisa diakses langsung dari luar class.",
                    "- Gunakan getter/setter method untuk akses data private secara aman.",
                    "",
                    "Langkah Praktik:",
                    "1. Pindahkan inisialisasi data product ke constructor.",
                    "2. Ubah harga menjadi private.",
                    "3. Buat method getHarga() dan setHarga() dengan validasi harga > 0.",
                    "",
                    "Latihan Singkat:",
                    "- Coba set harga negatif dan pastikan ditolak.",
                    "- Tampilkan pesan jika update harga gagal.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami inheritance untuk mewarisi struktur class.",
                    "- Mengurangi duplikasi kode antar class yang mirip.",
                    "",
                    "Materi Inti:",
                    "- Class anak dapat mewarisi property dan method dari class induk.",
                    "- Gunakan keyword extends untuk inheritance.",
                    "- Tambahkan perilaku khusus di class anak sesuai kebutuhan.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat class induk Item dengan property nama.",
                    "2. Buat class Ebook dan VideoCourse yang extends Item.",
                    "3. Tambahkan method khusus di masing-masing class anak.",
                    "",
                    "Latihan Singkat:",
                    "- Tampilkan daftar item campuran (ebook + video) dari satu array.",
                    "- Pastikan semua item tetap bisa menampilkan nama dari class induk.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami method overriding pada inheritance.",
                    "- Menyesuaikan perilaku method di class turunan.",
                    "",
                    "Materi Inti:",
                    "- Overriding terjadi saat method di child punya nama sama dengan parent.",
                    "- Gunakan parent::methodName() jika ingin memanggil versi parent.",
                    "- Cocok untuk kustomisasi output per tipe object.",
                    "",
                    "Langkah Praktik:",
                    "1. Tambahkan method tampilkanInfo() di class Item.",
                    "2. Override tampilkanInfo() pada Ebook dan VideoCourse.",
                    "3. Bandingkan output parent vs child.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan informasi durasi untuk VideoCourse.",
                    "- Pastikan output Ebook dan VideoCourse berbeda format.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami interface sebagai kontrak method.",
                    "- Menerapkan interface untuk menjaga konsistensi class.",
                    "",
                    "Materi Inti:",
                    "- Interface mendefinisikan method tanpa implementasi.",
                    "- Class yang implements interface wajib menulis method tersebut.",
                    "- Cocok untuk menyamakan perilaku banyak class berbeda.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat interface Publishable dengan method publish().",
                    "2. Implementasikan interface pada class Ebook dan VideoCourse.",
                    "3. Panggil publish() dari masing-masing object.",
                    "",
                    "Latihan Singkat:",
                    "- Buat 1 class baru Quiz yang juga implements Publishable.",
                    "- Uji bahwa semua class bisa diproses dalam loop yang sama.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami static method dan class constant di PHP.",
                    "- Menggunakan static untuk utilitas yang tidak tergantung object.",
                    "",
                    "Materi Inti:",
                    "- Static property/method diakses lewat nama class, bukan object.",
                    "- Constant class cocok untuk nilai tetap (contoh: pajak, status default).",
                    "- Hindari static untuk state yang harus berubah per object.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat class ProductHelper dengan static method formatRupiah().",
                    "2. Buat constant DEFAULT_STOCK pada class Product.",
                    "3. Gunakan keduanya saat menampilkan data produk.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah static method generateSku() sederhana.",
                    "- Gunakan constant untuk batas minimal stok.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami service layer sederhana dalam OOP.",
                    "- Memisahkan logic bisnis dari class entity/model.",
                    "",
                    "Materi Inti:",
                    "- Entity/class model fokus menyimpan struktur data.",
                    "- Service class fokus menjalankan process bisnis.",
                    "- Pola ini memudahkan testing dan maintenance.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat class ProductService.",
                    "2. Pindahkan logic hitung total nilai stok ke ProductService.",
                    "3. Panggil service dari script utama lalu tampilkan hasilnya.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan method filterProdukMahal() di service.",
                    "- Uji dengan data produk dummy yang berbeda harga.",
                ]),
                9 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menggabungkan seluruh konsep OOP ke mini project akhir.",
                    "- Menyusun struktur class yang rapi dan mudah dikembangkan.",
                    "",
                    "Materi Inti:",
                    "- Mini project: Manajemen Produk OOP.",
                    "- Struktur saran: Product.php, ProductService.php, ProductHelper.php, index.php.",
                    "- Fitur minimal: tambah data produk, tampilkan list, hitung nilai inventori.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat beberapa object Product dengan kategori berbeda.",
                    "2. Gunakan ProductService untuk proses data.",
                    "3. Tampilkan ringkasan laporan produk di index.php.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan fitur update stok via method class.",
                    "- Tambahkan ringkasan jumlah produk per kategori.",
                ]),
            ],
            'MVC Dasar (PHP Native) by Project' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami konsep dasar MVC (Model, View, Controller).",
                    "- Memahami alur request dari user sampai response tampil di browser.",
                    "",
                    "Materi Inti:",
                    "- Model bertugas mengelola data.",
                    "- View bertugas menampilkan tampilan ke user.",
                    "- Controller menjadi penghubung antara Model dan View.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat folder project mvc-native dengan struktur awal model, view, controller.",
                    "2. Buat file index.php sebagai entry point.",
                    "3. Simulasikan alur sederhana: request ke controller lalu render view.",
                    "",
                    "Latihan Singkat:",
                    "- Gambarkan alur request MVC versi kamu dalam 3 langkah.",
                    "- Tambahkan catatan fungsi setiap folder di file README sederhana.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami routing sederhana tanpa framework.",
                    "- Mengarahkan URL path ke controller yang tepat.",
                    "",
                    "Materi Inti:",
                    "- Routing bisa dibangun dari parsing URL request.",
                    "- Gunakan switch/if untuk mapping path ke aksi.",
                    "- Entry point tetap satu file agar alur mudah dikontrol.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file router.php.",
                    "2. Tambahkan route /home, /posts, /about ke controller masing-masing.",
                    "3. Tampilkan pesan berbeda untuk tiap route.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan route /contact.",
                    "- Buat fallback 404 untuk route yang tidak ditemukan.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami peran controller untuk logic bisnis.",
                    "- Menjaga file view tetap bersih dari proses data.",
                    "",
                    "Materi Inti:",
                    "- Controller menerima request dari router.",
                    "- Controller mengolah data (sementara dummy data) sebelum kirim ke view.",
                    "- Controller membantu kode lebih terstruktur dan scalable.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat HomeController dan PostController.",
                    "2. Isi method index() untuk menyiapkan data sederhana.",
                    "3. Kirim data dari controller ke view dan tampilkan di halaman.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan method show() untuk detail post berdasarkan id.",
                    "- Tampilkan pesan jika id post tidak ditemukan.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami peran Model untuk pengelolaan data.",
                    "- Memisahkan akses data dari controller.",
                    "",
                    "Materi Inti:",
                    "- Model bertanggung jawab membaca/menulis data.",
                    "- Untuk tahap awal, data bisa berupa array dummy.",
                    "- Controller cukup memanggil method dari model.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat class PostModel.",
                    "2. Tambahkan method all() dan findById().",
                    "3. Panggil model dari PostController.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan method latest() untuk 3 post terbaru.",
                    "- Kembalikan null jika id tidak ditemukan.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami peran View untuk tampilan HTML.",
                    "- Menampilkan data dari controller ke view.",
                    "",
                    "Materi Inti:",
                    "- View fokus di presentasi, bukan logic bisnis.",
                    "- Data dari controller di-render di template sederhana.",
                    "- Gunakan include agar layout bisa dipakai ulang.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file view home.php dan posts.php.",
                    "2. Kirim data posts dari controller ke posts.php.",
                    "3. Tampilkan judul dan ringkasan post di halaman.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan view detail post.",
                    "- Buat partial header.php untuk dipakai di semua view.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Mengintegrasikan router, controller, model, dan view.",
                    "- Melihat alur MVC secara end-to-end.",
                    "",
                    "Materi Inti:",
                    "- Request masuk ke router.",
                    "- Router memanggil controller.",
                    "- Controller mengambil data dari model lalu render view.",
                    "",
                    "Langkah Praktik:",
                    "1. Uji route /posts menampilkan list dari model.",
                    "2. Uji route /posts/show?id=1 menampilkan detail.",
                    "3. Uji route tidak valid menghasilkan 404.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan route /posts/latest.",
                    "- Pastikan semua route pakai pola controller yang konsisten.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Merapikan struktur folder project MVC.",
                    "- Menjaga project tetap mudah dibaca saat fitur bertambah.",
                    "",
                    "Materi Inti:",
                    "- Pisahkan folder: app/Controllers, app/Models, app/Views.",
                    "- Tambahkan folder core untuk router dan helper.",
                    "- Gunakan naming file yang konsisten.",
                    "",
                    "Langkah Praktik:",
                    "1. Pindahkan file sesuai struktur baru.",
                    "2. Perbarui path require/include agar tetap berjalan.",
                    "3. Cek ulang semua route setelah refactor.",
                    "",
                    "Latihan Singkat:",
                    "- Buat file README struktur folder.",
                    "- Tambahkan aturan naming sederhana untuk tim.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyelesaikan mini project Blog MVC Sederhana.",
                    "- Menggabungkan semua konsep MVC native PHP.",
                    "",
                    "Materi Inti:",
                    "- Fitur minimal: list post, detail post, halaman about.",
                    "- Gunakan controller terpisah per modul.",
                    "- Pastikan struktur folder rapi dan mudah dipelihara.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat modul Post lengkap (list + detail).",
                    "2. Buat halaman statis About dan Home.",
                    "3. Siapkan halaman 404 untuk route invalid.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan kategori post sederhana.",
                    "- Tambahkan widget post terbaru di sidebar.",
                ]),
            ],
            'Vue 3 Dasar by Project' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyiapkan project Vue 3 dasar.",
                    "- Memahami struktur file utama Vue.",
                    "",
                    "Materi Inti:",
                    "- Vue app dijalankan dari main.ts/main.js.",
                    "- Component utama biasanya App.vue.",
                    "- Struktur rapi mempermudah scale project.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat project Vue dengan Vite.",
                    "2. Jalankan project dan cek di browser.",
                    "3. Rapikan folder components dan pages.",
                    "",
                    "Latihan Singkat:",
                    "- Buat komponen HelloStudent.vue.",
                    "- Tampilkan komponen di App.vue.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami template syntax dan data reaktif.",
                    "- Menampilkan data dinamis di template.",
                    "",
                    "Materi Inti:",
                    "- Gunakan moustache syntax untuk render data.",
                    "- Data reaktif dibuat lewat ref()/reactive().",
                    "- Saat data berubah, tampilan ikut update.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat variabel namaCourse dan level di script setup.",
                    "2. Tampilkan data di template.",
                    "3. Ubah nilai data dan lihat update otomatis.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan data deskripsi course.",
                    "- Tampilkan data dalam card sederhana.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami event handling di Vue.",
                    "- Merespon aksi klik user.",
                    "",
                    "Materi Inti:",
                    "- Event ditulis dengan directive @click, @input, dll.",
                    "- Method handler ditempatkan di script setup.",
                    "- Event membantu membuat UI interaktif.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat tombol untuk increment counter.",
                    "2. Tampilkan nilai counter di halaman.",
                    "3. Tambahkan tombol reset counter.",
                    "",
                    "Latihan Singkat:",
                    "- Buat tombol toggle tampil/sembunyi deskripsi.",
                    "- Tambah event mouseover untuk highlight card.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami computed dan watch.",
                    "- Mengelola turunan data dan reaksi perubahan.",
                    "",
                    "Materi Inti:",
                    "- computed untuk nilai turunan yang efisien.",
                    "- watch untuk side-effect saat data berubah.",
                    "- Gunakan computed saat hanya butuh hasil turunan.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat computed untuk status level course.",
                    "2. Buat watch untuk mencetak log saat keyword berubah.",
                    "3. Uji perubahan data dari input.",
                    "",
                    "Latihan Singkat:",
                    "- Buat computed jumlah item todo aktif.",
                    "- Buat watch yang memberi notifikasi saat list kosong.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami komunikasi antar komponen dengan props dan emit.",
                    "- Mengirim data dari parent ke child dan sebaliknya.",
                    "",
                    "Materi Inti:",
                    "- Props untuk data turun dari parent ke child.",
                    "- Emit untuk event naik dari child ke parent.",
                    "- Pola ini menjaga alur data tetap jelas.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat komponen CourseCard menerima props title dan level.",
                    "2. Tambahkan tombol di child untuk emit event pilih-course.",
                    "3. Tangkap event di parent dan tampilkan hasil pilihan.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan props boolean isActive.",
                    "- Emit event hapus-course dari child ke parent.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menggunakan conditional rendering.",
                    "- Menampilkan UI berbeda berdasarkan kondisi.",
                    "",
                    "Materi Inti:",
                    "- Gunakan v-if/v-else untuk blok kondisional.",
                    "- Gunakan v-show jika hanya butuh hide/show via CSS.",
                    "- Pilih directive sesuai kebutuhan performa.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat status loading boolean.",
                    "2. Saat loading true tampilkan skeleton.",
                    "3. Saat false tampilkan daftar course.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah kondisi empty state saat data kosong.",
                    "- Tampilkan badge khusus untuk course premium.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menampilkan daftar data dengan v-for.",
                    "- Mengelola key list dengan benar.",
                    "",
                    "Materi Inti:",
                    "- v-for digunakan untuk render data array.",
                    "- Setiap item wajib key unik agar render stabil.",
                    "- Hindari menggunakan index sebagai key jika ada id.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat array courses berisi id, title, level.",
                    "2. Render list card course dengan v-for.",
                    "3. Tambahkan tampilan nomor urut lesson.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan fitur hapus item dari list.",
                    "- Tambahkan filter list berdasarkan level.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami form binding dengan v-model.",
                    "- Menangani input form secara reaktif.",
                    "",
                    "Materi Inti:",
                    "- v-model menyederhanakan binding input.",
                    "- Bisa dipakai untuk text, select, checkbox.",
                    "- Validasi awal dapat dibuat sebelum submit.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat form tambah todo dengan input judul.",
                    "2. Ikat input ke state dengan v-model.",
                    "3. Saat submit, push data ke list todo.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah validasi: judul tidak boleh kosong.",
                    "- Tambah field prioritas (low/medium/high).",
                ]),
                9 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menggunakan Composition API dengan script setup.",
                    "- Menulis komponen Vue yang ringkas dan modern.",
                    "",
                    "Materi Inti:",
                    "- script setup mengurangi boilerplate code.",
                    "- ref/computed/watch digunakan langsung tanpa return.",
                    "- Cocok untuk komponen kecil sampai menengah.",
                    "",
                    "Langkah Praktik:",
                    "1. Refactor komponen todo ke script setup.",
                    "2. Pisahkan logic ke fungsi-fungsi kecil.",
                    "3. Pastikan fitur lama tetap berjalan.",
                    "",
                    "Latihan Singkat:",
                    "- Buat reusable composable useTodoFilter().",
                    "- Gunakan composable itu di dua komponen.",
                ]),
                10 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyelesaikan mini project Task Tracker Vue 3.",
                    "- Menggabungkan semua materi Vue dasar dalam 1 produk.",
                    "",
                    "Materi Inti:",
                    "- Fitur minimal: tambah task, tandai selesai, filter task.",
                    "- Gunakan komponen terpisah untuk list dan form.",
                    "- Terapkan state lokal tanpa store global.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat halaman utama task tracker.",
                    "2. Integrasikan form, list, dan filter.",
                    "3. Rapikan UI dan struktur komponen.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan hitung progress task selesai.",
                    "- Tambahkan fitur edit judul task.",
                ]),
            ],
            'Laravel Dasar by Project' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami struktur dasar project Laravel.",
                    "- Mengetahui fungsi folder penting di Laravel.",
                    "",
                    "Materi Inti:",
                    "- Folder app berisi logic aplikasi.",
                    "- Folder routes untuk definisi endpoint.",
                    "- Folder resources untuk view/frontend asset.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat project Laravel baru.",
                    "2. Jelajahi folder utama dan catat fungsinya.",
                    "3. Jalankan server lokal dengan artisan serve.",
                    "",
                    "Latihan Singkat:",
                    "- Buat catatan singkat struktur folder utama.",
                    "- Tunjukkan 3 folder yang paling sering dipakai.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami routing dan controller di Laravel.",
                    "- Mengarahkan endpoint web ke method controller.",
                    "",
                    "Materi Inti:",
                    "- Route didefinisikan di routes/web.php.",
                    "- Controller menyimpan logic per endpoint.",
                    "- Pisahkan route dan logic agar kode bersih.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat HomeController.",
                    "2. Daftarkan route /home ke method index.",
                    "3. Return response sederhana dari controller.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan route /about.",
                    "- Tambahkan method about di controller yang sama.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menampilkan data ke Blade view.",
                    "- Memahami alur controller ke template.",
                    "",
                    "Materi Inti:",
                    "- Gunakan return view() dari controller.",
                    "- Data bisa dikirim sebagai array.",
                    "- Blade memudahkan render data dan layout.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat file resources/views/home.blade.php.",
                    "2. Kirim variabel judul dan deskripsi dari controller.",
                    "3. Tampilkan variabel tersebut di Blade.",
                    "",
                    "Latihan Singkat:",
                    "- Buat layout sederhana dengan @yield.",
                    "- Gunakan layout itu untuk halaman about.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami migration dan seeder untuk database.",
                    "- Menyiapkan struktur tabel dan data awal.",
                    "",
                    "Materi Inti:",
                    "- Migration untuk versioning schema database.",
                    "- Seeder untuk data awal/testing.",
                    "- Jalankan migrasi dengan artisan migrate.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat migration tabel courses.",
                    "2. Jalankan migrate.",
                    "3. Buat seeder course lalu jalankan db:seed.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan kolom level dan total_lessons.",
                    "- Seed minimal 3 course data.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami Model dan Eloquent ORM dasar.",
                    "- Melakukan query data melalui model.",
                    "",
                    "Materi Inti:",
                    "- Model mewakili tabel di database.",
                    "- Eloquent menyediakan query yang mudah dibaca.",
                    "- Gunakan fillable untuk proteksi mass assignment.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat model Course.",
                    "2. Ambil semua data course dari controller.",
                    "3. Kirim data ke view lalu tampilkan list.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan query where berdasarkan level.",
                    "- Urutkan course berdasarkan total_lessons.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menerapkan validasi dengan Form Request.",
                    "- Menjaga data input tetap valid sebelum diproses.",
                    "",
                    "Materi Inti:",
                    "- Form Request memisahkan rule validasi dari controller.",
                    "- Rule validasi ditulis dalam method rules().",
                    "- Error validasi otomatis tersedia ke view.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat StoreCourseRequest.",
                    "2. Definisikan validasi title, level, total_lessons.",
                    "3. Gunakan request ini di controller store().",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan custom message untuk 1 field.",
                    "- Tampilkan error validasi di form.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami service layer untuk logic bisnis.",
                    "- Menjaga controller tetap tipis dan fokus flow.",
                    "",
                    "Materi Inti:",
                    "- Service class menampung logic yang bisa dipakai ulang.",
                    "- Controller memanggil service, bukan query kompleks langsung.",
                    "- Pola ini memudahkan testing dan maintenance.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat CourseService.",
                    "2. Pindahkan logic list/filter course ke service.",
                    "3. Panggil service dari controller.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan method getCourseById di service.",
                    "- Gunakan method tersebut di halaman detail.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami relasi database dasar di Laravel.",
                    "- Menghubungkan tabel yang saling terkait.",
                    "",
                    "Materi Inti:",
                    "- hasMany dan belongsTo paling sering digunakan.",
                    "- Relasi memudahkan ambil data terkait.",
                    "- Gunakan eager loading untuk menghindari N+1 query.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat model Course dan Lesson dengan relasi.",
                    "2. Tambahkan migration lessons dengan foreign key course_id.",
                    "3. Tampilkan jumlah lesson per course.",
                    "",
                    "Latihan Singkat:",
                    "- Tampilkan daftar lesson di halaman detail course.",
                    "- Uji eager loading dengan with('lessons').",
                ]),
                9 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami pagination dan query filter.",
                    "- Menangani data list yang semakin banyak.",
                    "",
                    "Materi Inti:",
                    "- paginate() memecah data menjadi halaman.",
                    "- Filter bisa digabung dengan query builder.",
                    "- Query string penting agar filter tetap aktif saat pindah page.",
                    "",
                    "Langkah Praktik:",
                    "1. Terapkan paginate pada list course.",
                    "2. Tambahkan filter search dan level.",
                    "3. Pertahankan query filter saat klik pagination.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah sorting title asc/desc.",
                    "- Tambah reset filter ke nilai default.",
                ]),
                10 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami error handling dasar di Laravel.",
                    "- Menangani skenario data tidak ditemukan dengan aman.",
                    "",
                    "Materi Inti:",
                    "- Gunakan findOrFail untuk otomatis 404.",
                    "- Gunakan try-catch untuk error proses tertentu.",
                    "- Flash message membantu feedback ke user.",
                    "",
                    "Langkah Praktik:",
                    "1. Terapkan findOrFail di detail course.",
                    "2. Tambahkan flash success/error setelah aksi.",
                    "3. Buat halaman 404 sederhana.",
                    "",
                    "Latihan Singkat:",
                    "- Simulasikan akses id yang tidak ada.",
                    "- Tampilkan pesan yang jelas saat validasi gagal.",
                ]),
                11 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyusun mini project Backend Kelas Online.",
                    "- Menggabungkan semua materi Laravel dasar.",
                    "",
                    "Materi Inti:",
                    "- Modul utama: course, tutorial, challenge.",
                    "- Gunakan service + request pattern untuk backend.",
                    "- Pastikan flow data dari route ke controller jelas.",
                    "",
                    "Langkah Praktik:",
                    "1. Bangun endpoint web untuk list dan detail course.",
                    "2. Integrasikan validasi dan service layer.",
                    "3. Rapikan struktur folder dan naming class.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan fitur publish/unpublish course.",
                    "- Tambahkan statistik jumlah tutorial per course.",
                ]),
            ],
            'Inertia.js Dasar untuk Laravel' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami konsep Laravel + Inertia + Vue.",
                    "- Mengenali flow tanpa API REST terpisah.",
                    "",
                    "Materi Inti:",
                    "- Controller langsung render page Vue via Inertia.",
                    "- Props dikirim dari backend ke frontend.",
                    "- Routing tetap dikelola Laravel.",
                    "",
                    "Langkah Praktik:",
                    "1. Install Inertia dan adapter Vue.",
                    "2. Buat halaman Home Inertia pertama.",
                    "3. Render page dari controller.",
                    "",
                    "Latihan Singkat:",
                    "- Buat halaman About dengan Inertia.",
                    "- Kirim 1 props string dari controller ke page.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami Inertia::render dari controller.",
                    "- Mengontrol data page dari backend.",
                    "",
                    "Materi Inti:",
                    "- Inertia::render menerima nama page dan props.",
                    "- Penamaan page harus sesuai file Vue.",
                    "- Data tetap divalidasi di backend sebelum dikirim.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat CourseController@index.",
                    "2. Kembalikan Inertia::render('Courses', [...]).",
                    "3. Tampilkan props di Courses.vue.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah props total_course.",
                    "- Tampilkan heading dinamis berdasarkan props.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami passing props dari Laravel ke Vue.",
                    "- Menjaga bentuk data props konsisten.",
                    "",
                    "Materi Inti:",
                    "- Props sebaiknya dinormalisasi di controller/service.",
                    "- Gunakan type di Vue agar data aman.",
                    "- Hindari kirim data berlebihan ke frontend.",
                    "",
                    "Langkah Praktik:",
                    "1. Definisikan type Course di script setup.",
                    "2. Mapping data course di controller.",
                    "3. Render card course dari props array.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah field level badge di card.",
                    "- Tambah fallback jika array kosong.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Membuat layout dan komponen shared.",
                    "- Menghindari duplikasi UI antar halaman.",
                    "",
                    "Materi Inti:",
                    "- Layout digunakan membungkus semua page.",
                    "- Komponen shared contoh: header, breadcrumb, card.",
                    "- Struktur folder rapi memudahkan maintain.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat AppLayout.vue.",
                    "2. Pindahkan header ke komponen AppHeader.vue.",
                    "3. Pakai AppLayout di Home dan Courses.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan slot action di header.",
                    "- Tambahkan breadcrumb reusable component.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami form submission dengan Inertia.",
                    "- Mengirim data tanpa API endpoint terpisah.",
                    "",
                    "Materi Inti:",
                    "- Gunakan form helper Inertia di Vue.",
                    "- Controller tetap menerima request Laravel biasa.",
                    "- Validasi tetap di backend.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat form submit challenge sederhana.",
                    "2. Kirim request POST ke route Laravel.",
                    "3. Tampilkan hasil submit di halaman detail.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan loading state saat submit.",
                    "- Disable tombol saat proses berjalan.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Mengelola flash message dan error handling.",
                    "- Memberi feedback aksi ke pengguna.",
                    "",
                    "Materi Inti:",
                    "- Flash message dibagikan via middleware Inertia.",
                    "- Error validasi otomatis tersedia di page props.",
                    "- Feedback cepat meningkatkan UX belajar.",
                    "",
                    "Langkah Praktik:",
                    "1. Share flash success di HandleInertiaRequests.",
                    "2. Tampilkan flash alert di layout.",
                    "3. Uji dengan submit berhasil dan gagal.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan style berbeda untuk success/error.",
                    "- Tambahkan tombol close alert.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Memahami navigasi antar page Inertia.",
                    "- Menjaga pengalaman SPA-like di web Laravel.",
                    "",
                    "Materi Inti:",
                    "- Gunakan komponen Link dari Inertia.",
                    "- Navigasi tetap memakai route Laravel.",
                    "- State page bisa dipertahankan sesuai kebutuhan.",
                    "",
                    "Langkah Praktik:",
                    "1. Ganti anchor biasa ke Link Inertia.",
                    "2. Buat alur Home -> Courses -> Detail.",
                    "3. Uji bahwa transisi halaman tanpa full reload.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan tombol back ke list.",
                    "- Tambahkan active link di header.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyelesaikan mini project Course List Interaktif.",
                    "- Menggabungkan semua dasar Inertia ke 1 alur.",
                    "",
                    "Materi Inti:",
                    "- List course, detail course, dan navigasi lesson.",
                    "- Props dari backend jadi sumber data utama.",
                    "- Tidak perlu API terpisah untuk tahap awal.",
                    "",
                    "Langkah Praktik:",
                    "1. Lengkapi halaman Courses dengan card interaktif.",
                    "2. Tambahkan halaman detail dan tombol mulai tutorial.",
                    "3. Uji seluruh flow route-controller-inertia-vue.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan filter level di list course.",
                    "- Tambahkan progress badge di halaman detail.",
                ]),
            ],
            'Full Project: Laravel + Inertia + Vue' => [
                1 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyusun requirement produk e-learning.",
                    "- Menentukan fitur MVP yang realistis.",
                    "",
                    "Materi Inti:",
                    "- Requirement harus jelas: siapa user, masalah, solusi.",
                    "- Prioritaskan fitur inti dulu.",
                    "- Hindari fitur berlebih di fase awal.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat dokumen requirement singkat.",
                    "2. Tetapkan 3 fitur wajib MVP.",
                    "3. Tentukan flow belajar utama user.",
                    "",
                    "Latihan Singkat:",
                    "- Buat 3 user story utama.",
                    "- Tandai mana fitur now/later.",
                ]),
                2 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Mendesain struktur data utama project.",
                    "- Menentukan tabel dan relasi inti.",
                    "",
                    "Materi Inti:",
                    "- Entity inti: course, tutorial, challenge, submission.",
                    "- Tentukan relasi dan field penting lebih awal.",
                    "- Struktur data baik memudahkan coding berikutnya.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat ERD sederhana.",
                    "2. Tentukan migration yang dibutuhkan.",
                    "3. Susun urutan implementasi tabel.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan field progress pada rancangan.",
                    "- Jelaskan alasan memilih relasi satu-ke-banyak.",
                ]),
                3 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Membangun modul Course end-to-end.",
                    "- Menyediakan list dan detail course.",
                    "",
                    "Materi Inti:",
                    "- Course jadi pintu masuk utama e-learning.",
                    "- Buat controller, service, dan page terpisah.",
                    "- Pastikan payload frontend konsisten.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat migration dan model Course.",
                    "2. Buat CourseService + CourseController.",
                    "3. Tampilkan halaman list dan detail.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah filter level course.",
                    "- Tambah sorting berdasarkan jumlah lesson.",
                ]),
                4 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Membangun modul Tutorial per course.",
                    "- Menyediakan daftar lesson dan detail materi.",
                    "",
                    "Materi Inti:",
                    "- Tutorial adalah alur belajar bertahap.",
                    "- Detail lesson harus mudah dibaca pemula.",
                    "- Navigasi next/previous meningkatkan flow belajar.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat migration + model CourseTutorial.",
                    "2. Buat route list/detail tutorial.",
                    "3. Buat page list dan detail tutorial.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan estimasi durasi per lesson.",
                    "- Tambahkan status selesai/belum via session.",
                ]),
                5 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Membangun modul Challenge per course.",
                    "- Menyediakan latihan praktik setelah tutorial.",
                    "",
                    "Materi Inti:",
                    "- Challenge menguji pemahaman lesson.",
                    "- Butuh alur submit jawaban dan feedback.",
                    "- Simpan submission untuk evaluasi progres.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat tabel challenge dan submissions.",
                    "2. Buat controller untuk list/detail/submit.",
                    "3. Tampilkan status submit di halaman challenge.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan validasi minimal panjang jawaban.",
                    "- Tambahkan pesan sukses setelah submit.",
                ]),
                6 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menerapkan service + request layer secara konsisten.",
                    "- Menjaga controller tetap tipis.",
                    "",
                    "Materi Inti:",
                    "- Request class fokus validasi input.",
                    "- Service class fokus logic bisnis.",
                    "- Controller fokus orkestrasi flow.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat request class untuk semua endpoint penting.",
                    "2. Pindahkan logic query ke service.",
                    "3. Rapikan dependensi controller via constructor.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah method service untuk progres belajar.",
                    "- Tambah unit checklist untuk memastikan pola konsisten.",
                ]),
                7 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyempurnakan UI halaman Course List.",
                    "- Menjadikan halaman list sebagai dashboard belajar.",
                    "",
                    "Materi Inti:",
                    "- Card perlu menampilkan info inti course.",
                    "- Tambahkan filter/sort agar mudah eksplorasi.",
                    "- UI harus sederhana tapi jelas.",
                    "",
                    "Langkah Praktik:",
                    "1. Rapikan card title, level, lesson count.",
                    "2. Tambahkan search dan filter level.",
                    "3. Tambahkan CTA ke detail course.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan badge status progress.",
                    "- Tambahkan empty-state saat data kosong.",
                ]),
                8 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyempurnakan UI halaman Course Detail.",
                    "- Memudahkan user lanjut ke tutorial/challenge.",
                    "",
                    "Materi Inti:",
                    "- Detail page harus menampilkan konteks lengkap course.",
                    "- CTA utama mengarah ke tutorial.",
                    "- Progress ringkas meningkatkan motivasi belajar.",
                    "",
                    "Langkah Praktik:",
                    "1. Tampilkan deskripsi, level, total lesson, challenge count.",
                    "2. Tambahkan progress card + status label.",
                    "3. Tambahkan tombol mulai tutorial dan lihat challenge.",
                    "",
                    "Latihan Singkat:",
                    "- Tambah breadcrumb agar navigasi jelas.",
                    "- Tambah rekomendasi course berikutnya.",
                ]),
                9 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyempurnakan UI Tutorial Detail.",
                    "- Membuat materi lesson nyaman dibaca.",
                    "",
                    "Materi Inti:",
                    "- Struktur materi harus konsisten tiap lesson.",
                    "- Navigasi antar lesson harus jelas.",
                    "- Status selesai membantu tracking progres.",
                    "",
                    "Langkah Praktik:",
                    "1. Tambahkan box panduan belajar cepat.",
                    "2. Tambahkan tombol next/previous lesson.",
                    "3. Tambahkan aksi tandai selesai.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan tombol batalkan selesai.",
                    "- Tambahkan indikator durasi belajar.",
                ]),
                10 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyempurnakan UI Challenge Detail.",
                    "- Memudahkan user submit hasil praktik.",
                    "",
                    "Materi Inti:",
                    "- Challenge harus punya instruksi jelas.",
                    "- Form submit perlu validasi dan feedback.",
                    "- Status submission perlu terlihat user.",
                    "",
                    "Langkah Praktik:",
                    "1. Tampilkan deskripsi challenge + target output.",
                    "2. Buat form jawaban dengan textarea.",
                    "3. Tampilkan hasil submit terbaru.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan checklist self-review sebelum submit.",
                    "- Tambahkan riwayat submit sederhana.",
                ]),
                11 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Mengintegrasikan alur tutorial ke challenge.",
                    "- Mendorong belajar praktik setelah materi.",
                    "",
                    "Materi Inti:",
                    "- Setelah lesson selesai, user diarahkan ke challenge relevan.",
                    "- Progress tutorial dan challenge harus sinkron.",
                    "- Flow belajar perlu minim friksi.",
                    "",
                    "Langkah Praktik:",
                    "1. Tambahkan CTA challenge di akhir tutorial detail.",
                    "2. Hubungkan lesson tertentu ke challenge tertentu.",
                    "3. Tampilkan status challenge pada halaman tutorial list.",
                    "",
                    "Latihan Singkat:",
                    "- Tambahkan label \"Siap Challenge\" saat lesson selesai.",
                    "- Tambahkan notifikasi saat challenge pertama berhasil submit.",
                ]),
                12 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Melakukan refactor dan cleanup project.",
                    "- Menjaga codebase tetap mudah dikembangkan.",
                    "",
                    "Materi Inti:",
                    "- Hapus duplikasi logic yang tidak perlu.",
                    "- Rapikan naming class, file, dan variable.",
                    "- Tambahkan komentar seperlunya untuk bagian kompleks.",
                    "",
                    "Langkah Praktik:",
                    "1. Audit controller agar tetap tipis.",
                    "2. Rapikan service dan request naming.",
                    "3. Rapikan struktur komponen Vue.",
                    "",
                    "Latihan Singkat:",
                    "- Buat checklist refactor yang sudah selesai.",
                    "- Tandai area yang masih technical debt.",
                ]),
                13 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menjalankan testing manual skenario utama.",
                    "- Memastikan flow inti aplikasi stabil.",
                    "",
                    "Materi Inti:",
                    "- Fokus dulu pada happy path.",
                    "- Uji list, detail, tutorial, challenge, dan progress.",
                    "- Catat bug dan prioritas perbaikan.",
                    "",
                    "Langkah Praktik:",
                    "1. Buat daftar test case manual.",
                    "2. Uji setiap route utama satu per satu.",
                    "3. Catat hasil dan issue yang ditemukan.",
                    "",
                    "Latihan Singkat:",
                    "- Buat tabel hasil uji (case, expected, actual).",
                    "- Tambahkan 3 edge case penting untuk diuji.",
                ]),
                14 => implode("\n", [
                    "Tujuan Belajar:",
                    "- Menyiapkan rilis MVP pertama project.",
                    "- Menyusun langkah deploy dan handover.",
                    "",
                    "Materi Inti:",
                    "- Pastikan environment production siap.",
                    "- Dokumentasi setup penting untuk keberlanjutan.",
                    "- MVP fokus pada fitur inti yang stabil.",
                    "",
                    "Langkah Praktik:",
                    "1. Final check konfigurasi .env dan APP_KEY.",
                    "2. Jalankan migrate + seed pada server target.",
                    "3. Buat dokumentasi install dan run project.",
                    "",
                    "Latihan Singkat:",
                    "- Buat release note versi MVP v1.",
                    "- Buat rencana pengembangan versi berikutnya (v1.1).",
                ]),
            ],
        ];
    }

    /**
     * Daftar roadmap topik per course agar alur belajar berjenjang.
     *
     * @return array<string, array<int, string>>
     */
    private function courseTopics(): array
    {
        return [
            'PHP Dasar by Project' => [
                'Setup Project PHP + Database',
                'Koneksi Database dengan PDO',
                'Query SELECT dan Tampilkan Data',
                'Query INSERT Data',
                'Query UPDATE Data',
                'Query DELETE Data',
                'Prepared Statement + Validasi Input',
                'Fitur Search dengan LIKE',
                'Pagination Sederhana',
                'Mini Project: CRUD Catatan Keuangan',
            ],
            'OOP PHP Dasar by Project' => [
                'Pengenalan Class dan Object',
                'Property dan Method',
                'Constructor dan Encapsulation',
                'Inheritance Dasar',
                'Method Overriding',
                'Interface dan Implementasi',
                'Static Method dan Constant',
                'Studi Kasus: Service Layer Sederhana',
                'Mini Project: Manajemen Produk OOP',
            ],
            'MVC Dasar (PHP Native) by Project' => [
                'Konsep MVC dan Alur Request',
                'Routing Sederhana Native PHP',
                'Controller untuk Logika Bisnis',
                'Model untuk Data',
                'View untuk Tampilan',
                'Integrasi MVC End to End',
                'Refactor Struktur Folder',
                'Mini Project: Blog MVC Sederhana',
            ],
            'Vue 3 Dasar by Project' => [
                'Setup Vue 3 + TypeScript',
                'Komponen Dasar dengan script setup',
                'Props dan Emit Antar Komponen',
                'Reactive State dan Event Handling',
                'Loop dan Conditional Rendering',
                'Form Input dengan v-model',
                'Computed untuk Filter Data',
                'Simpan Data ke LocalStorage',
                'Refactor ke Komponen Reusable',
                'Mini Project: Task Tracker Vue',
            ],
            'Laravel Dasar by Project' => [
                'Struktur Dasar Laravel',
                'Routing dan Controller',
                'Blade dan View Data',
                'Migration dan Seeder',
                'Model dan Eloquent Dasar',
                'Form Request Validation',
                'Service Layer untuk Logic',
                'Relasi Database Dasar',
                'Pagination dan Query Filter',
                'Error Handling Dasar',
                'Mini Project: Backend Kelas Online',
            ],
            'Inertia.js Dasar untuk Laravel' => [
                'Konsep Laravel + Inertia',
                'Inertia Render dari Controller',
                'Props dari Laravel ke Vue',
                'Layout dan Shared Components',
                'Form Submission Inertia',
                'Flash Message dan Error Handling',
                'Navigasi Antar Page Inertia',
                'Mini Project: Course List Interaktif',
            ],
            'Full Project: Laravel + Inertia + Vue' => [
                'Menyusun Requirement Product',
                'Mendesain Struktur Data',
                'Membuat Modul Course',
                'Membuat Modul Tutorial',
                'Membuat Modul Challenge',
                'Menyusun Service dan Request Layer',
                'Membangun UI Course List',
                'Membangun UI Course Detail',
                'Membangun UI Tutorial Detail',
                'Membangun UI Challenge Detail',
                'Integrasi Alur Tutorial ke Challenge',
                'Refactor dan Cleanup Project',
                'Testing Manual Skenario Utama',
                'Rilis Versi MVP',
            ],
        ];
    }
}

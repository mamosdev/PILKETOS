<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Election;
use App\Models\Voter;
use App\Enums\ElectionStatus;
use App\Support\TokenGenerator;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // migrate:fresh menghapus tabel users, jadi akun panitia dibuat ulang di sini
        User::firstOrCreate(
            ['email' => 'admin@pilketos.test'],
            ['name' => 'Panitia', 'password' => 'password'] // hanya untuk demo lokal
        );

        $election = Election::create([
            'title' => 'Pemilihan Ketua OSIS 2026',
            'slug' => 'pilketos-2026',
            'organization' => 'MAN 1 Surakarta',
            'status' => ElectionStatus::Active,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(4),
        ]);

        $election->candidates()->create([
            'number' => '01',
            'name' => 'Arya Pratama',
            'running_mate' => 'Nisa Aulia',
            'subtitle' => 'XI MIPA 1 & XI IPS 2',
            'tagline' => 'Inovasi Digital, Prestasi Maksimal, OSIS Inklusif untuk Semua.',
            'vision' => 'Mewujudkan OSIS MAN 1 Surakarta sebagai wadah kolaborasi siswa ...',
            'missions' => [
                'Membangun portal aspirasi siswa berbasis digital ...',
                'Mengoptimalkan publikasi dan dukungan pendanaan ...',
                'Menyelenggarakan festival kreativitas ...',
            ],
            'programs' => [
                ['title' => 'SuaraKita App', 'desc' => 'Kotak aspirasi digital & pelacakan progres usulan siswa.'],
                ['title' => 'Nusantara Expo', 'desc' => 'Pekan karya seni, teknologi, dan bazar wirausaha siswa.'],
            ],
            'sort_order' => 1,
        ]);
        // Kandidat 02 dan 03: salin dari array `candidates` di prototipe (baris 941-984).
        // Pemetaan: ketua → name, wakil → running_mate, kelas → subtitle,
        //           visi → vision, misi → missions, programs → programs.

        // Pemilih demo yang sama dengan modal login prototipe
        $demo = Voter::create([
            'identifier' => '0084920199', 'name' => 'Raka Pratama',
            'group_name' => 'XI MIPA 2', 'cohort' => 'XI',
        ]);
        $this->enroll($election, $demo, 'OSIS26');

        Voter::factory(59)->create()->each(fn ($voter) => $this->enroll($election, $voter));
    }

    private function enroll(Election $election, Voter $voter, ?string $token = null): void
    {
        $election->participations()->create([
            'voter_id' => $voter->id,
            'token_hash' => Hash::make($token ?? TokenGenerator::make()),
            'token_issued_at' => now(),
        ]);
    }
}

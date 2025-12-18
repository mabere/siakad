<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class EdomQuestionnaireSeeder extends Seeder
{
    public function run()
    {
        // Ambil tahun akademik aktif
        $academicYear = AcademicYear::where('status', 1)->first();

        if (!$academicYear) {
            throw new \Exception('Tidak ada tahun akademik yang aktif!');
        }

        // Buat template kuesioner EDOM
        $questionnaire = Questionnaire::create([
            'title' => 'Evaluasi Dosen Mengajar (EDOM)',
            'description' => 'Kuesioner evaluasi kinerja dosen dalam proses pembelajaran',
            'type' => 'EDOM',
            'status' => 'ACTIVE',
            'academic_year_id' => $academicYear->id
        ]);

        // Pertanyaan untuk setiap kategori
        $questions = [
            '1' => [
                'Dosen menjelaskan silabus (RPS) dengan jelas dan sistematis.',
                'Dosen menggunakan bahasa pengantar yang sederhana dan mudah dipahami.',
                'Dosen memilih dan memanfaatkan media pembelajaran yang sesuai dengan materi.',
                'Dosen memberikan tugas/penugasan yang memadai untuk memperdalam pemahaman.',
                'Dosen mendorong interaksi dan diskusi aktif antar mahasiswa dalam kelas.'
            ],
            '2' => [
                'Dosen menguasai materi perkuliahan dengan baik dan menyajikan contoh aplikasi terkini.',
                'Dosen menggunakan buku acuan dan literatur mutakhir (kurang dari 5–10 tahun).',
                'Dosen memanfaatkan teknologi (e-learning, proyektor, alat peraga) untuk memperkaya materi.',
                'Dosen menerapkan metode pembelajaran yang memfasilitasi keterlibatan mahasiswa.',
                'Dosen memberikan umpan balik (feedback) yang konstruktif atas hasil tugas mahasiswa.'
            ],
            '3' => [
                'Dosen menunjukkan rasa percaya diri dalam menyampaikan materi.',
                'Dosen memiliki kewibawaan dan sikap profesional di hadapan mahasiswa.',
                'Dosen mengambil keputusan yang bijaksana dalam menangani pertanyaan/masalah mahasiswa.',
                'Dosen bersikap jujur dan adil dalam menilai tugas dan ujian.',
                'Dosen menunjukkan konsistensi antara ucapan dan tindakan (integritas).'
            ],
            '4' => [
                'Dosen menghargai perbedaan latar belakang dan pendapat mahasiswa.',
                'Dosen membangun suasana kelas yang inklusif dan kolaboratif.',
                'Dosen mampu mendengarkan dan merespon keluhan atau saran mahasiswa.',
                'Dosen memfasilitasi kerja sama kelompok dalam kegiatan pembelajaran.',
                'Dosen bersedia membantu mahasiswa di luar jam perkuliahan (konsultasi).'
            ],
            '5' => [
                'Dosen menunjukkan kemampuan komunikasi yang efektif (clear messaging).',
                'Dosen menginspirasi dan memotivasi mahasiswa untuk berpikir kritis.',
                'Dosen membimbing mahasiswa dalam pengembangan kemampuan pemecahan masalah.',
                'Dosen menumbuhkan semangat kerja sama (teamwork) dalam tugas/tantangan kelompok.',
                'Dosen memperlihatkan empati dan kepedulian terhadap kesulitan mahasiswa.'
            ]
        ];

        // Insert pertanyaan
        foreach ($questions as $category => $items) {
            foreach ($items as $index => $question) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_text' => $question,
                    'type' => 'RATING',
                    'category' => $category,
                    'weight' => 1.00
                ]);
            }
        }
    }
}

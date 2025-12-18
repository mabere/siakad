<?php

return [
    'admin' => [
        ['heading' => 'MASTER'],
        [
            'title' => 'Akademik',
            'icon' => 'ni ni-building',
            'sub' => [
                ['title' => 'Fakultas', 'route' => 'admin.faculty.index'],
                ['title' => 'Program Studi', 'route' => 'admin.prodi.index'],
                ['title' => 'Mata Kuliah', 'route' => 'curriculums.index'],
            ]
        ],
        [
            'title' => 'Manajemen Surat',
            'icon' => 'ni ni-book-read',
            'sub' => [
                ['title' => 'Permohonan', 'route' => 'admin.letter-requests.index'],
                ['title' => 'Tipe Surat', 'route' => 'admin.letter-types.index'],
                ['title' => 'Verifikasi', 'route' => 'verify.form'],
            ]
        ],
        [
            'title' => 'SDM',
            'icon' => 'ni ni-users',
            'sub' => [
                ['title' => 'Dosen', 'route' => 'admin.dosen.index'],
                ['title' => 'Mahasiswa', 'route' => 'admin.mhs.index'],
                ['title' => 'Pegawai', 'route' => 'admin.pegawai.index'],
                ['title' => 'UPT & Lembaga', 'route' => 'admin.units.index'],
            ]
        ],
        [
            'title' => 'Tri Dharma Dosen',
            'icon' => 'ni ni-tranx',
            'sub' => [
                [
                    'title' => 'Pendidikan',
                    'icon' => 'ni ni-book',
                    'sub' => [
                        ['title' => 'Pendidikan', 'route' => '#'],
                    ]
                ],
                [
                    'title' => 'Penelitian',
                    'icon' => 'ni ni-search',
                    'sub' => [
                        ['title' => 'Penelitian', 'route' => 'admin.publication.index'],
                    ]
                ],
                [
                    'title' => 'PKM',
                    'icon' => 'ni ni-share',
                    'sub' => [
                        ['title' => 'Pengabdian', 'route' => 'admin.pkm.index'],
                    ]
                ],
                [
                    'title' => 'Penunjang',
                    'icon' => 'ni ni-dot-box',
                    'sub' => [
                        ['title' => 'Penunjang', 'route' => 'admin.penunjang.index'],
                        ['title' => 'Dashboard Penunjang', 'route' => 'admin.penunjang.dashboard.detail'],
                        ['title' => 'Validasi Penunjang', 'route' => 'admin.penunjang.validation.list'],
                    ]
                ],
            ]
        ],
        [
            'title' => 'Pengaturan',
            'icon' => 'ni ni-setting',
            'sub' => [
                ['title' => 'Kelola Jadwal', 'route' => 'admin.list-jadwal.prodi'],
                ['title' => 'Tahun Akademik', 'route' => 'admin.ta.index'],
                ['title' => 'Kelas', 'route' => 'admin.kelas.index'],
                ['title' => 'Users', 'route' => 'admin.users.index'],
            ]
        ],
        [
            'title' => 'Skripsi',
            'icon' => 'ni ni-book-fill',
            'sub' => [
                ['title' => 'Pembimbingan', 'route' => 'admin.thesis.supervision.index'],
            ]
        ],
        [
            'title' => 'Fasilitas',
            'icon' => 'ni ni-menu-squared',
            'sub' => [
                ['title' => 'Gedung', 'route' => 'admin.gedung.index'],
                ['title' => 'Ruangan', 'route' => 'admin.ruangan.index'],
                ['title' => 'Sarpras', 'route' => 'admin.sarpras.index'],
            ]
        ],
        ['heading' => 'EVALUASI'],
        [
            'title' => 'Alumni',
            'icon' => 'ni ni-chart-up',
            'sub' => [
                ['title' => 'Daftar Alumni', 'route' => 'admin.alumni.index'],
                ['title' => 'Laporan', 'route' => 'alumni.reports'],
            ]
        ],
        [
            'title' => 'Dashboard EDOM',
            'icon' => 'ni ni-clipboard',
            'sub' => [
                [
                    'title' => 'Kuesioner',
                    'route' => 'admin.edom.questionnaire.index',
                ],
                [
                    'title' => 'Pengaturan EDOM',
                    'route' => 'admin.edom.settings.index',
                ],
                [
                    'title' => 'Pertanyaan',
                    'route' => 'admin.edom.questionnaire.index',
                ],
                [
                    'title' => 'Laporan',
                    'icon' => 'ni ni-reports',
                    'sub' => [
                        [
                            'title' => 'Laporan Utama',
                            'route' => 'admin.edom.reports.index',
                        ],
                        [
                            'title' => 'Detail Departemen',
                            'route' => 'admin.edom.reports.departments',
                        ],
                        [
                            'title' => 'Export PDF',
                            'route' => 'admin.edom.reports.export.pdf',
                        ],
                    ],
                ],
            ],
        ],

        [
            'title' => 'Monitoring',
            'icon' => 'ni ni-reports-alt',
            'sub' => [
                ['title' => 'Perkuliahan', 'route' => 'admin.monitoring.index'],
            ]
        ],

    ],

    'dekan' => [
        ['heading' => 'MANAJEMEN SURAT'],
        [
            'title' => 'Manajemen Surat',
            'icon' => 'ni ni-file-docs',
            'sub' => [
                ['title' => 'Permintaan Surat', 'route' => 'dekan.request.surat-masuk.index'],
                ['title' => 'Kelola Tipe Surat', 'route' => 'dekan.letter-types.index'],
            ],
        ],

        ['heading' => 'AKADEMIK'],
        [
            'title' => 'Program Studi',
            'icon' => 'ni ni-building',
            'sub' => [
                ['title' => 'Laporan', 'route' => 'dekan.departments.index'],
                ['title' => 'Laporan Akademik', 'route' => 'dekan.academic.index'],
            ],
        ],
        [
            'title' => 'Ujian Skripsi',
            'icon' => 'ni ni-book',
            'sub' => [
                ['title' => 'Ujian Skripsi', 'route' => 'dekan.thesis.exam.index'],
            ],
        ],
        [
            'title' => 'Mahasiswa',
            'icon' => 'ni ni-users',
            'sub' => [
                ['title' => 'Statistik', 'route' => 'dekan.department.student-statistics'],
            ],
        ],

        ['heading' => 'EVALUASI DOSEN'],
        [
            'title' => 'EDOM',
            'icon' => 'ni ni-bar-chart',
            'sub' => [
                ['title' => 'Laporan Evaluasi', 'route' => 'dekan.edom.index'],
            ],
        ],

        ['heading' => 'KALENDER AKADEMIK'],
        [
            'title' => 'Tambah Kegiatan',
            'icon' => 'ni ni-calendar',
            'route' => 'dekan.kegiatan.akademik.index',
        ],
    ],

    'kaprodi' => [
        ['heading' => 'AKADEMIK'],
        [
            'title' => 'Master Data',
            'icon' => 'ni ni-db-fill',
            'sub' => [
                ['title' => 'Dosen', 'route' => 'kaprodi.dosen.index'],
                ['title' => 'Mahasiswa', 'route' => 'kaprodi.mahasiswa.index'],
                ['title' => 'Kelas', 'route' => 'kaprodi.kelas.index'],
                ['title' => 'Perbaikan Nilai', 'route' => 'kaprodi.remedial.index'],
                ['title' => 'Validasi Nilai', 'route' => 'kaprodi.nilai.validasi'],
            ],
        ],
        ['heading' => 'TUGAS AKHIR'],
        [
            'title' => 'Ujian',
            'icon' => 'ni ni-book',
            'sub' => [
                ['title' => 'Ujian Skripsi', 'route' => 'kaprodi.thesis.exam.index'],
            ],
        ],
        ['heading' => 'PERSURATAN'],
        [
            'title' => 'Persuratan',
            'icon' => 'ni ni-building',
            'sub' => [
                ['title' => 'Permintaan', 'route' => 'kaprodi.request.surat-masuk.index'],
                ['title' => 'Kelola Tipe Surat', 'route' => 'kaprodi.letter-types.index'],
            ],
        ],

        [
            'title' => 'Evaluasi',
            'icon' => 'ni ni-check-round-cut',
            'sub' => [
                ['title' => 'Edom', 'route' => 'kaprodi.edom.index'],
                ['title' => 'Laporan', 'route' => 'kaprodi.edom.reports'],
            ],
        ],
    ],

    'dosen' => [
        ['heading' => 'KEGIATAN AKADEMIK'],
        [
            'title' => 'Perkuliahan',
            'icon' => 'ni ni-book',
            'sub' => [
                [
                    'title' => 'Pelaksanaan',
                    'icon' => 'ni ni-growth',
                    'sub' => [
                        ['title' => 'Jadwal', 'route' => 'lecturer.schedules'],
                        ['title' => 'Presensi', 'route' => 'lecturer.attendance.index'],
                        ['title' => 'BAP', 'route' => 'lecturer.bap.index'],
                        ['title' => 'Nilai', 'route' => 'lecturer.nilai.index'],
                        // ['title' => 'Perbaikan Nilai', 'route' => 'dosen.remedial.index'],
                    ]
                ],
                ['title' => 'Riwayat Mengajar', 'icon' => 'ni ni-growth', 'route' => 'lecturer.riwayat.mengajar'],
            ]
        ],
        // [
        //     'title' => 'Tri Dharma',
        //     'icon' => 'ni ni-dashboard-fill',
        //     'sub' => [
        //         [
        //             'title' => 'Penelitian',
        //             'icon' => 'ni ni-search',
        //             'sub' => [
        //                 ['title' => 'Penelitian', 'route' => 'lecturer.publication.index'],
        //             ]
        //         ],
        //         [
        //             'title' => 'Pengabdian',
        //             'icon' => 'ni ni-share',
        //             'sub' => [
        //                 ['title' => 'Pengabdian', 'route' => 'lecturer.pkm.index'],
        //             ]
        //         ],
        //         [
        //             'title' => 'Penunjang',
        //             'icon' => 'ni ni-grid-plus',
        //             'sub' => [
        //                 ['title' => 'Data Penunjang', 'route' => 'lecturer.penunjang.index'],
        //                 ['title' => 'Dashboard Penunjang', 'route' => 'lecturer.penunjang.dashboard'],
        //             ]
        //         ],
        //     ]
        // ],
        // [
        //     'title' => 'Pembimbingan',
        //     'icon' => 'ni ni-reports',
        //     'sub' => [
        //         ['title' => 'Bimbingan Skripsi', 'route' => 'lecturer.thesis.supervision.index'],
        //         ['title' => 'Penasehat Akademik', 'route' => 'lecturer.krs.index'],
        //     ]
        // ],
        // ['heading' => 'EVALUASI AKADEMIK'],
        // [
        //     'title' => 'Perkuliahan',
        //     'icon' => 'ni ni-monitor',
        //     'sub' => [
        //         [
        //             'title' => 'Monitoring Perkuliahan',
        //             'icon' => 'ni ni-monitor',
        //             'route' => 'lecturer.monitoring.index',
        //         ],
        //         [
        //             'title' => 'EDOM',
        //             'icon' => 'ni ni-monitor',
        //             'route' => 'lecturer.edom.index',
        //         ]
        //     ]
        // ],
    ],

    'mahasiswa' => [
        ['heading' => 'AKADEMIK'],
        [
            'title' => 'Akademik',
            'icon' => 'ni ni-building-fill',
            'sub' => [
                ['title' => 'KRS', 'route' => 'student.krs.index'],
                ['title' => 'Jadwal', 'route' => 'student.jadwal'],
                ['title' => 'Presensi', 'route' => 'student.presensi'],
                // ['title' => 'KHS', 'route' => 'student.nilai.index'],
            ]
        ],
        // [
        //     'title' => 'Persuratan',
        //     'icon' => 'ni ni-book-read',
        //     'sub' => [
        //         ['title' => 'Permohonan Surat', 'route' => 'student.request.surat.index'],
        //     ]
        // ],
        ['heading' => 'EVALUASI'],
        [
            'title' => 'Evaluasi',
            'icon' => 'ni ni-edit',
            'sub' => [
                ['title' => 'EDOM', 'route' => 'student.edom.index'],
            ]
        ],
        // [
        //     'title' => 'Perkuliahan',
        //     'icon' => 'ni ni-file-check',
        //     'sub' => [
        //         ['title' => 'Remedial', 'route' => 'mhs.remedial.index'],
        //     ]
        // ],
        // ['heading' => 'TUGAS AKHIR'],
        // [
        //     'title' => 'Bimbingan',
        //     'icon' => 'ni ni-file-docs',
        //     'sub' => [
        //         ['title' => 'Bimbingan Skripsi', 'route' => 'student.thesis.supervision.index'],
        //     ]
        // ],
        // [
        //     'title' => 'Ujian',
        //     'icon' => 'ni ni-edit',
        //     'sub' => [
        //         ['title' => 'Informasi Pendaftaran', 'route' => 'student.thesis.exam.index'],
        //         ['title' => 'Pendaftaran', 'route' => 'mahasiswa.thesis.index'],
        //     ]
        // ],

    ],

    'staff' => [
        ['heading' => 'MASTER OK'],
        [
            'title' => 'AKADEMIK',
            'icon' => 'ni ni-chart-up',
            'sub' => [
                ['title' => 'Program Studi', 'route' => 'staff.department.index'],
                ['title' => 'Mata Kuliah', 'route' => 'staff.course.index'],
                ['title' => 'Validasi Nilai', 'route' => 'staff.nilai.validasi'],
                ['title' => 'Permohonan Surat', 'route' => 'staff.letter-request.index'],
            ]
        ],
        [
            'title' => 'SDM',
            'icon' => 'ni ni-users-fill',
            'sub' => [
                ['title' => 'Dosen', 'route' => 'staff.dosen.index'],
                ['title' => 'Mahasiswa', 'route' => 'staff.mahasiswa.index'],
            ]
        ],

        ['heading' => 'PENGATURAN OK'],
        [
            'title' => 'Akademik',
            'icon' => 'ni ni-book-read',
            'sub' => [
                ['title' => 'Tambah Jadwal', 'route' => 'staff.jadwal.create'],
                ['title' => 'Jadwal', 'route' => 'staff.jadwal.index'],
                ['title' => 'Kelas', 'route' => 'staff.kelas.index'],
                ['title' => 'Dosen PA', 'route' => 'staff.mahasiswa.advisor'],
                ['title' => 'Perbaikan Nilai', 'route' => 'staff.remedial.index'],
            ]
        ],
    ],

    'ktu' => [
        ['heading' => 'MASTER'],
        [
            'title' => 'AKADEMIK',
            'icon' => 'ni ni-chart-up',
            'sub' => [
                ['title' => 'Program Studi', 'route' => 'ktu.department.index'],
                ['title' => 'Dosen', 'route' => 'ktu.dosen.index'],
                ['title' => 'Mahasiswa', 'route' => 'ktu.mahasiswa.index'],
            ]
        ],
        ['heading' => 'PERSURATAN'],
        [
            'title' => 'Akademik',
            'icon' => 'ni ni-book-read',
            'sub' => [
                ['title' => 'Permohonan Surat', 'route' => 'ktu.letter-request.index'],
            ]
        ],
        ['heading' => 'TUGAS AKHIR'],
        [
            'title' => 'Ujian Skripsi',
            'icon' => 'ni ni-user-list-fill',
            'sub' => [
                ['title' => 'Pendaftaran Ujian', 'route' => 'ktu.thesis.exam.index'],
                ['title' => 'Jadwal Ujian', 'route' => 'jadwal.ujian.index'],
            ]
        ],
    ],

];

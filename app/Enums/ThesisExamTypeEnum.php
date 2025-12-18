<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum ThesisExamTypeEnum: string
{
    case Proposal = 'proposal';
    case Hasil = 'hasil';
    case Tertutup = 'tertutup';

    /**
     * Get a human-readable label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::Proposal => 'Ujian Proposal',
            self::Hasil => 'Ujian Hasil',
            self::Tertutup => 'Ujian Tertutup',
        };
    }

    /**
     * Get all labels as a collection.
     *
     * @return \Illuminate\Support\Collection<string, string>
     */
    public static function labels(): Collection
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label(),
        ]);
    }
}
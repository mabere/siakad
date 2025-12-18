<?php

namespace App\Services\LetterRequest;

use App\Models\LetterRequest;
use App\Models\User;

interface LetterRequestService
{
    public function approve(LetterRequest $letterRequest, User $user);
    public function reject(LetterRequest $letterRequest, User $user, string $reason);
    public function generatePdf(LetterRequest $letterRequest, $faculty, $student, array $additionalData = []);
    public function generateReferenceNumber(LetterRequest $letterRequest);
    public function download(LetterRequest $letterRequest);
}
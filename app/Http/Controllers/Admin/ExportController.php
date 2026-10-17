<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CandidateExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(private readonly CandidateExportService $export) {}

    public function candidates(Request $request): StreamedResponse
    {
        $this->authorize('export', User::class);

        $candidates = User::query()
            ->where('role', User::ROLE_CANDIDATE)
            ->with(['district', 'skills'])
            ->orderBy('created_at')
            ->get();

        return $this->export->stream($candidates, $request->user());
    }
}

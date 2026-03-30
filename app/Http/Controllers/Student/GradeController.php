<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    private const QUARTER_TERMS = [
        'First Quarter',
        'Second Quarter',
        'Third Quarter',
        'Fourth Quarter',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->account_type === 'student', 403);

        $entries = $this->approvedEntriesQuery($user)
            ->with(['subject', 'approver'])
            ->orderByRaw('FIELD(term, ?, ?, ?, ?)', self::QUARTER_TERMS)
            ->orderByDesc('approved_at')
            ->get();

        $gradesLiveSignature = $this->buildLiveSignature($user);

        $quarterTerms = self::QUARTER_TERMS;
        $activeTerm = (string) $request->query('term', '');

        if (!in_array($activeTerm, $quarterTerms, true)) {
            $activeTerm = collect($quarterTerms)->first(function (string $term) use ($entries) {
                return $entries->contains(function ($entry) use ($term) {
                    return (string) $entry->term === $term;
                });
            }) ?? $quarterTerms[0];
        }

        return view('student.grades.index', compact('entries', 'quarterTerms', 'activeTerm', 'gradesLiveSignature'));
    }

    public function liveSignature(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $user->account_type === 'student', 403);

        return response()->json([
            'signature' => $this->buildLiveSignature($user),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    private function approvedEntriesQuery(User $student)
    {
        return GradeEntry::query()
            ->where('student_id', $student->id)
            ->whereIn('term', self::QUARTER_TERMS)
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere(function ($approvedQuery) {
                        $approvedQuery->whereNotNull('approved_by')
                            ->whereNotNull('approved_at');
                    });
            });
    }

    private function buildLiveSignature(User $student): string
    {
        $entriesQuery = $this->approvedEntriesQuery($student);
        $statusCounts = (clone $entriesQuery)
            ->selectRaw('LOWER(status) as status_key, COUNT(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key')
            ->toArray();
        ksort($statusCounts);

        $payload = [
            'student_id' => (int) $student->id,
            'student_section_id' => (int) ($student->section_id ?? 0),
            'student_updated_at' => $this->timestampOrZero($student->updated_at),
            'entry_count' => (clone $entriesQuery)->count(),
            'entry_updated_at' => $this->timestampOrZero((clone $entriesQuery)->max('updated_at')),
            'entry_approved_at' => $this->timestampOrZero((clone $entriesQuery)->max('approved_at')),
            'status_counts' => $statusCounts,
            'terms_hash' => md5(json_encode(self::QUARTER_TERMS)),
        ];

        return hash('sha256', json_encode($payload));
    }

    private function timestampOrZero(mixed $value): string
    {
        if (empty($value)) {
            return '0';
        }

        if ($value instanceof \DateTimeInterface) {
            return (string) $value->getTimestamp();
        }

        $timestamp = strtotime((string) $value);
        return $timestamp ? (string) $timestamp : '0';
    }
}

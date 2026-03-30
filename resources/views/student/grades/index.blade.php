@extends('layouts.app-student')

@section('title', 'My Grades - Student Dashboard - Amore Academy')

@section('content')
@vite(['resources/css/layouts/dashboard-roles/dashboard-student.css','resources/css/student/grade-view.css'])

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

  .grades-dashboard {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #0f172a;
  }

  .grades-hero-card {
    border-radius: 24px;
    background: linear-gradient(130deg, #15803d 0%, #10b981 100%);
    box-shadow: 0 20px 35px rgba(16, 185, 129, 0.25);
    color: #ffffff;
    padding: 1.5rem;
  }

  .grades-hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
  }

  .grades-hero-subtitle {
    margin-top: 0.55rem;
    margin-bottom: 0;
    opacity: 0.95;
    font-size: 0.98rem;
  }

  .grades-metrics-grid {
    margin-top: 1.25rem;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.9rem;
  }

  .metric-card {
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 0.95rem 1rem;
    backdrop-filter: blur(2px);
  }

  .metric-label {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.9;
  }

  .metric-value {
    font-size: 1.5rem;
    line-height: 1.1;
    margin-top: 0.3rem;
    font-weight: 700;
  }

  .metric-subtext {
    margin-top: 0.25rem;
    font-size: 0.82rem;
    opacity: 0.88;
  }

  .grades-main-card {
    border-radius: 22px;
    border: 1px solid #d9f2e4;
    background: #ffffff;
    box-shadow: 0 18px 30px rgba(22, 101, 52, 0.08);
    padding: 1.3rem;
  }

  .grades-main-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.8rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
  }

  .grades-main-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: #14532d;
  }

  .grades-main-caption {
    margin: 0.25rem 0 0;
    color: #64748b;
    font-size: 0.9rem;
  }

  .grades-main-meta {
    border-radius: 999px;
    padding: 0.35rem 0.75rem;
    background: #ecfdf3;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 0.82rem;
    font-weight: 600;
  }

  .grade-quarter-tabs {
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .grade-quarter-tabs .nav-link {
    border: 1px solid rgba(22, 101, 52, 0.25);
    border-radius: 999px;
    color: #166534;
    font-weight: 600;
    padding: 0.5rem 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.2s ease;
    background: #ffffff;
  }

  .grade-quarter-tabs .nav-link:hover {
    transform: translateY(-1px);
    border-color: #15803d;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.14);
  }

  .grade-quarter-tabs .nav-link.active {
    background: #166534;
    border-color: #166534;
    color: #ffffff;
    box-shadow: 0 10px 18px rgba(22, 101, 52, 0.28);
  }

  .quarter-count {
    font-size: 0.78rem;
    line-height: 1;
    border-radius: 999px;
    padding: 0.2rem 0.48rem;
    background: rgba(15, 23, 42, 0.08);
    color: inherit;
  }

  .grade-quarter-tabs .nav-link.active .quarter-count {
    background: rgba(255, 255, 255, 0.22);
  }

  .term-summary-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.8rem;
    margin-bottom: 0.9rem;
  }

  .term-summary-card {
    border: 1px solid #dcfce7;
    background: #f8fffb;
    border-radius: 14px;
    padding: 0.7rem 0.85rem;
  }

  .term-summary-label {
    font-size: 0.78rem;
    color: #64748b;
    margin-bottom: 0.2rem;
  }

  .term-summary-value {
    font-size: 1.03rem;
    font-weight: 700;
    color: #14532d;
    margin: 0;
  }

  .grades-table-wrap {
    border: 1px solid #dbece0;
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
  }

  .grades-table {
    margin-bottom: 0;
    min-width: 680px;
  }

  .grades-table thead th {
    background: #f3faf6;
    color: #14532d;
    border-bottom: 1px solid #d9f2e4;
    font-weight: 700;
    letter-spacing: 0.01em;
    padding: 0.95rem 1rem;
  }

  .grades-table tbody td {
    padding: 0.95rem 1rem;
    border-top: 1px solid #edf4ef;
    vertical-align: middle;
  }

  .grades-table tbody tr {
    transition: all 0.22s ease;
  }

  .grades-table tbody tr:hover {
    background: #f7fff9;
    transform: translateX(3px);
  }

  .subject-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    background: #ecfdf3;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
  }

  .grade-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 76px;
    border-radius: 999px;
    font-weight: 700;
    padding: 0.35rem 0.65rem;
    font-size: 0.92rem;
  }

  .grade-badge.excellent {
    background: #dcfce7;
    color: #166534;
  }

  .grade-badge.good {
    background: #ecfeff;
    color: #0f766e;
  }

  .grade-badge.fair {
    background: #fef9c3;
    color: #854d0e;
  }

  .grade-badge.needs-work {
    background: #fee2e2;
    color: #991b1b;
  }

  .approved-date {
    color: #1e293b;
    font-weight: 500;
  }

  .quarter-empty-state {
    padding: 2rem 1rem;
    text-align: center;
  }

  .quarter-empty-state i {
    color: #16a34a;
    font-size: 2rem;
    opacity: 0.85;
  }

  .quarter-empty-state h5 {
    margin-top: 0.65rem;
    margin-bottom: 0.35rem;
    color: #166534;
  }

  .quarter-empty-state p {
    color: #64748b;
    margin-bottom: 0;
  }

  @media (max-width: 991.98px) {
    .grades-hero-title {
      font-size: 1.65rem;
    }

    .grades-metrics-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 767.98px) {
    .grades-hero-card {
      border-radius: 18px;
      padding: 1.15rem;
    }

    .grades-hero-title {
      font-size: 1.35rem;
    }

    .grades-metrics-grid {
      grid-template-columns: 1fr;
    }

    .grades-main-card {
      border-radius: 16px;
      padding: 0.9rem;
    }

    .term-summary-row {
      grid-template-columns: 1fr;
    }

    .grade-quarter-tabs {
      gap: 0.5rem;
    }

    .grade-quarter-tabs .nav-link {
      padding: 0.45rem 0.78rem;
      font-size: 0.88rem;
    }
  }
</style>

<div class="dashboard-container student-grades-live-page"
  data-live-url="{{ route('student.grades.live-signature') }}"
  data-live-signature="{{ $gradesLiveSignature ?? '' }}">
  <div class="container-fluid px-4">
    <div class="row">
      <main class="col-12">
        @php
        $entriesByTerm = $entries->groupBy(function ($entry) {
        return (string) $entry->term;
        });

        $totalApproved = $entries->count();
        $overallAverage = $totalApproved > 0 ? number_format((float) $entries->avg('grade_value'), 2) : 'N/A';
        $latestApproval = optional($entries->sortByDesc('approved_at')->first())->approved_at;
        @endphp

        <div class="grades-dashboard">
          <div class="grades-hero-card mb-4">
            <h4 class="grades-hero-title">
              <i class="fas fa-graduation-cap"></i>
              My Approved Grades
            </h4>
            <p class="grades-hero-subtitle">Track your approved performance quarter by quarter in a single clean dashboard.</p>

            
          </div>

          <div class="grades-main-card">
            <div class="grades-main-header">
              <div>
                <h5 class="grades-main-title">Quarterly Grade Breakdown</h5>
                <p class="grades-main-caption">Use tabs to switch quarters. Table data updates instantly.</p>
              </div>
              <span class="grades-main-meta">{{ $quarterTerms ? count($quarterTerms) : 0 }} Terms</span>
            </div>

            <ul class="nav nav-pills grade-quarter-tabs" id="gradeQuarterTabs" role="tablist">
              @foreach($quarterTerms as $quarterTerm)
              @php
              $tabId = 'quarter-' . \Illuminate\Support\Str::slug($quarterTerm);
              $termEntries = $entriesByTerm->get($quarterTerm, collect());
              @endphp
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link {{ $activeTerm === $quarterTerm ? 'active' : '' }}"
                  id="{{ $tabId }}-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#{{ $tabId }}"
                  type="button"
                  role="tab"
                  aria-controls="{{ $tabId }}"
                  aria-selected="{{ $activeTerm === $quarterTerm ? 'true' : 'false' }}">
                  {{ $quarterTerm }}
                  <span class="quarter-count">{{ $termEntries->count() }}</span>
                </button>
              </li>
              @endforeach
            </ul>

            <div class="tab-content" id="gradeQuarterTabsContent">
              @foreach($quarterTerms as $quarterTerm)
              @php
              $tabId = 'quarter-' . \Illuminate\Support\Str::slug($quarterTerm);
              $termEntries = $entriesByTerm->get($quarterTerm, collect());
              $termAverage = $termEntries->count() > 0 ? number_format((float) $termEntries->avg('grade_value'), 2) : 'N/A';
              @endphp
              <div
                class="tab-pane fade {{ $activeTerm === $quarterTerm ? 'show active' : '' }}"
                id="{{ $tabId }}"
                role="tabpanel"
                aria-labelledby="{{ $tabId }}-tab"
                tabindex="0">
                <div class="term-summary-row">
                  <div class="term-summary-card">
                    <div class="term-summary-label">Approved Subjects</div>
                    <p class="term-summary-value">{{ $termEntries->count() }}</p>
                  </div>
                  <div class="term-summary-card">
                    <div class="term-summary-label">Quarter Average</div>
                    <p class="term-summary-value">{{ $termAverage }}</p>
                  </div>
                </div>

                <div class="grades-table-wrap">
                  <div class="table-responsive">
                    <table class="table grades-table align-middle">
                      <thead>
                        <tr>
                          <th style="width: 48%;">Subject</th>
                          <th style="width: 16%;">Grade</th>
                          <th style="width: 36%;">Approved At</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($termEntries as $entry)
                        @php
                        $gradeValue = (float) $entry->grade_value;
                        $gradeClass = 'needs-work';

                        if ($gradeValue >= 90) {
                        $gradeClass = 'excellent';
                        } elseif ($gradeValue >= 85) {
                        $gradeClass = 'good';
                        } elseif ($gradeValue >= 80) {
                        $gradeClass = 'fair';
                        }
                        @endphp
                        <tr>
                          <td>
                            <span class="subject-pill">{{ $entry->subject->name ?? 'Subject' }}</span>
                          </td>
                          <td>
                            <span class="grade-badge {{ $gradeClass }}">{{ number_format($gradeValue, 2) }}</span>
                          </td>
                          <td>
                            <span class="approved-date">{{ optional($entry->approved_at)->format('M d, Y h:i A') ?? 'Pending admin approval' }}</span>
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="3">
                            <div class="quarter-empty-state">
                              <i class="fas fa-info-circle"></i>
                              <h5>No approved grades for {{ $quarterTerm }}</h5>
                              <p>Approved grades will appear here once available.</p>
                            </div>
                          </td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var liveContainer = document.querySelector('.student-grades-live-page');
    var liveUrl = liveContainer ? (liveContainer.getAttribute('data-live-url') || '') : '';
    var liveSignature = liveContainer ? (liveContainer.getAttribute('data-live-signature') || '') : '';
    var liveRequestInFlight = false;
    var livePollTimer = null;

    function buildLiveUrl() {
      var url = new URL(liveUrl, window.location.origin);
      var currentParams = new URLSearchParams(window.location.search);

      currentParams.forEach(function(value, key) {
        url.searchParams.set(key, value);
      });

      return url.toString();
    }

    function checkLiveSignature() {
      if (!liveUrl || liveRequestInFlight) {
        return;
      }

      liveRequestInFlight = true;

      fetch(buildLiveUrl(), {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(function(response) {
          if (!response.ok) {
            return null;
          }

          return response.json();
        })
        .then(function(payload) {
          if (!payload || !payload.signature) {
            return;
          }

          var nextSignature = payload.signature;

          if (!liveSignature) {
            liveSignature = nextSignature;
            if (liveContainer) {
              liveContainer.setAttribute('data-live-signature', nextSignature);
            }
            return;
          }

          if (nextSignature !== liveSignature) {
            window.location.reload();
          }
        })
        .catch(function(error) {
          console.debug('Student grades live polling skipped:', error);
        })
        .finally(function() {
          liveRequestInFlight = false;
        });
    }

    if (!liveUrl) {
      return;
    }

    livePollTimer = window.setInterval(function() {
      if (!document.hidden) {
        checkLiveSignature();
      }
    }, 10000);

    document.addEventListener('visibilitychange', function() {
      if (!document.hidden) {
        checkLiveSignature();
      }
    });

    window.addEventListener('beforeunload', function() {
      if (livePollTimer) {
        clearInterval(livePollTimer);
        livePollTimer = null;
      }
    }, {
      once: true
    });
  });
</script>
@endsection
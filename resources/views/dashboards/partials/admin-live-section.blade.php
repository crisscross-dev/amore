<div class="row g-4 mb-4 dashboard-section" id="adminLiveSection" data-live-url="{{ route('dashboard.admin.live-section') }}">
    <div class="col-lg-8 dashboard-main-column">
        <div class="activity-card dashboard-notifications-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-clock-rotate-left me-2"></i>
                    Recent Logs
                </span>
            </div>

            <div class="card-body p-0">
                @if($recentLogs->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No recent logs found.</p>
                    </div>
                @else
                    <div class="table-responsive recent-logs-table-wrapper">
                        <table class="table enrollment-table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Performed By</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                    <tr>
                                        <td data-label="Activity">
                                            <div class="log-cell">
                                                <span class="log-title">{{ $log['title'] }}</span>
                                                <span class="log-detail">{{ $log['description'] }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Performed By"><span class="log-actor">{{ $log['actor'] }}</span></td>
                                        <td data-label="Time"><span class="log-time">{{ $log['time']?->timezone('Asia/Manila')->format('M d, g:i A') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($recentLogs->hasPages())
                        <div class="recent-log-pagination p-3 border-top bg-white">
                            {{ $recentLogs->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 dashboard-side-column">
        <div class="activity-card announcement-side-card h-100">
            <div class="card-header d-flex align-items-center">
                <span>
                    <i class="fas fa-bullhorn me-2"></i>
                    Announcement
                </span>
            </div>

            <div class="card-body p-0">
                @if($upcomingEvents->isEmpty() && $recentAnnouncements->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0">No upcoming events or announcements.</p>
                    </div>
                @else
                    <div class="announcement-scroll-area">
                        <div class="events-list p-3">
                            @if($upcomingEvents->isNotEmpty())
                                <div class="events-group-title">Events</div>
                                @foreach($upcomingEvents as $index => $event)
                                    @php
                                        $dateClass = ['event-date-pill', 'event-date-pill blue', 'event-date-pill violet'];
                                        $pillClass = $dateClass[$index % count($dateClass)];
                                        $eventTime = $event->is_all_day ? 'All Day' : $event->start_date->format('g A');
                                        $eventType = $event->event_type ? ucfirst($event->event_type) : 'General';
                                    @endphp
                                    <div class="event-item">
                                        <div class="{{ $pillClass }}">
                                            <span class="day">{{ $event->start_date->format('d') }}</span>
                                            <span class="month">{{ strtoupper($event->start_date->format('M')) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="event-title">{{ $event->title }}</h6>
                                            <p class="event-meta">{{ $eventType }} • {{ $eventTime }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($recentAnnouncements->isNotEmpty())
                                <div class="events-group-title {{ $upcomingEvents->isNotEmpty() ? 'mt-2' : '' }}">Announcements</div>
                                @foreach($recentAnnouncements as $announcement)
                                    <div class="announcement-item">
                                        <div class="announcement-icon">
                                            <i class="fas fa-bullhorn"></i>
                                        </div>
                                        <div>
                                            <h6 class="announcement-title">{{ $announcement->title }}</h6>
                                            <p class="announcement-meta">
                                                {{ ucfirst($announcement->priority ?? 'normal') }} • {{ $announcement->created_at?->format('M d') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white border-0 border-top p-3 text-center">
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-success px-4">
                    View All
                </a>
            </div>
        </div>
    </div>
</div>

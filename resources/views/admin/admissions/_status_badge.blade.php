@php
    $statusConfig = [
        'pending' => [
            'class' => 'status-pending',
            'icon' => 'fa-clock',
            'text' => 'Pending'
        ],
        'approved' => [
            'class' => 'status-approved',
            'icon' => 'fa-check-circle',
            'text' => 'Approved'
        ],
        'rejected' => [
            'class' => 'status-rejected',
            'icon' => 'fa-times-circle',
            'text' => 'Rejected'
        ],
        'waitlisted' => [
            'class' => 'status-waitlisted',
            'icon' => 'fa-hourglass-half',
            'text' => 'Waitlisted'
        ],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['pending'];
@endphp

<span class="status-badge {{ $config['class'] }}">
    <i class="fas {{ $config['icon'] }}"></i>
    {{ $config['text'] }}
</span>

<div class="modal fade" id="assignmentTimePickerModal" tabindex="-1" aria-labelledby="assignmentTimePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentTimePickerModalLabel">
                    <i class="fas fa-clock me-2"></i>Set Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="schedule-picker-columns">
                    <div class="picker-column" id="assignmentTimePickerDayColumn">
                        <div class="picker-column-title">Day</div>
                        <div class="picker-column-body" id="assignmentTimePickerDayList">
                            @foreach(($dayOptions ?? []) as $day)
                            <button type="button" class="picker-option" data-picker="day" data-value="{{ $day }}">
                                {{ $day }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="picker-column" id="assignmentTimePickerRoomColumn">
                        <div class="picker-column-title">Room</div>
                        <div class="picker-column-body" id="assignmentTimePickerRoomList">
                            @foreach(($roomOptions ?? []) as $room)
                            <button type="button" class="picker-option" data-picker="room" data-value="{{ $room }}">
                                {{ $room }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="picker-column" id="assignmentTimePickerTimeColumn">
                        <div class="picker-column-title">Time</div>
                        <div class="picker-column-body">
                            <div class="time-picker-list" id="assignmentTimePickerList">
                                @foreach(($timeOptions ?? []) as $time)
                                <button type="button" class="time-slot" data-index="{{ $loop->index }}">
                                    {{ $time }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="assignmentTimePickerClearBtn">Clear</button>
                <button type="button" class="btn btn-success" id="assignmentTimePickerApplyBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
@vite('resources/css/admin/schedule-picker-modal.css')
@endpush

@push('scripts')
<script>
    window.getSchedulePickerElements = function() {
        return {
            modalElement: document.getElementById('assignmentTimePickerModal'),
            dayList: document.getElementById('assignmentTimePickerDayList'),
            roomList: document.getElementById('assignmentTimePickerRoomList'),
            timeList: document.getElementById('assignmentTimePickerList'),
            dayColumn: document.getElementById('assignmentTimePickerDayColumn'),
            roomColumn: document.getElementById('assignmentTimePickerRoomColumn'),
            timeColumn: document.getElementById('assignmentTimePickerTimeColumn'),
            applyButton: document.getElementById('assignmentTimePickerApplyBtn'),
            clearButton: document.getElementById('assignmentTimePickerClearBtn')
        };
    };
</script>
@endpush
@endonce
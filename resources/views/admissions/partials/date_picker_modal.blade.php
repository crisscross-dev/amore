@once
@push('styles')
@vite('resources/css/admissions/date-picker-modal.css')
@endpush
@endonce

<div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="datePickerModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Select Date
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="date-picker-columns">
                    <div class="picker-column" id="datePickerYearColumn">
                        <div class="picker-column-title">Year</div>
                        <div class="picker-column-body" id="datePickerYearList"></div>
                    </div>
                    <div class="picker-column" id="datePickerMonthColumn">
                        <div class="picker-column-title">Month</div>
                        <div class="picker-column-body" id="datePickerMonthList"></div>
                    </div>
                    <div class="picker-column" id="datePickerDayColumn">
                        <div class="picker-column-title">Day</div>
                        <div class="picker-column-body" id="datePickerDayList"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="datePickerClearBtn">Clear</button>
                <button type="button" class="btn btn-success" id="datePickerApplyBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

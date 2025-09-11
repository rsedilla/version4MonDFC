<div class="flex justify-center">
    <input 
        type="checkbox" 
        class="rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
        {{ $checked ? 'checked' : '' }}
        data-record-id="{{ $record->id ?? '' }}"
        data-week="{{ $week }}"
        onchange="handleAttendanceChange(this, {{ $record->id ?? 0 }}, {{ $week }})"
    >
</div>

<script>
function handleAttendanceChange(checkbox, recordId, week) {
    console.log('Attendance changed:', {
        recordId: recordId,
        week: week,
        checked: checkbox.checked
    });
    
    // You can add your logic here when ready to implement backend
    // For now, just showing the frontend interaction
}
</script>

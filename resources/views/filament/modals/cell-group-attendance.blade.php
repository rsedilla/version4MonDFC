<div class="space-y-6">
    <!-- Header -->
    <div class="text-center border-b pb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            Cell Group Attendance Members
        </h3>
        <p class="text-sm text-gray-600 mt-1">
            Found {{ $cellGroupsData->count() }} cell groups with attendance records
        </p>
    </div>

    @if($cellGroupsData->isNotEmpty())
        <!-- Cell Groups List -->
        <div class="space-y-6">
            @foreach($cellGroupsData as $cellGroupData)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <!-- Cell Group Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">
                                {{ $cellGroupData['cell_group_name'] }}
                            </h4>
                            <p class="text-sm text-blue-600">
                                Cell Group ID: {{ $cellGroupData['cell_group_id'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $cellGroupData['total_attendees'] }} members
                            </span>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $cellGroupData['total_records'] }} total records
                            </p>
                        </div>
                    </div>

                    <!-- Members List -->
                    @if(count($cellGroupData['attendees']) > 0)
                        <div class="space-y-3">
                            <p class="text-sm font-medium text-gray-700 mb-3">Members attending:</p>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($cellGroupData['attendees'] as $index => $attendee)
                                    <div class="bg-white rounded-lg border border-gray-200 p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                        {{ $index + 1 }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">
                                                        {{ $attendee['name'] }}
                                                    </h5>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $attendee['type'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $attendee['present_count'] }} present
                                                    </span>
                                                    @if($attendee['absent_count'] > 0)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ $attendee['absent_count'] }} absent
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $attendee['attendance_rate'] }}% attendance rate
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Overall Summary -->
        <div class="bg-gray-50 rounded-lg p-4 border-t">
            <h4 class="font-medium text-gray-900 mb-3">Overall Summary</h4>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $cellGroupsData->count() }}</p>
                    <p class="text-sm text-gray-600">Cell Groups</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $cellGroupsData->sum('total_attendees') }}</p>
                    <p class="text-sm text-gray-600">Total Members</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600">{{ $cellGroupsData->sum('total_records') }}</p>
                    <p class="text-sm text-gray-600">Total Records</p>
                </div>
            </div>
        </div>
    @else
        <!-- No data found -->
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Attendance Data Found</h3>
            <p class="text-sm text-gray-600">No attendance records found in any cell groups.</p>
        </div>
    @endif
</div>

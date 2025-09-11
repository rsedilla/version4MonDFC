<div class="space-y-6">
    <!-- Header -->
    <div class="text-center border-b pb-4">
        <h3 class="text-xl font-bold text-gray-900">
            {{ $cellGroup['cell_group_name'] }} Cell Group
        </h3>
        <p class="text-sm text-gray-600 mt-1">
            Led by {{ $cellGroup['leader_name'] }} ({{ $cellGroup['leader_type'] }})
        </p>
    </div>

    <!-- Cell Group Information -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h4 class="text-lg font-medium text-green-900 mb-3">Cell Group Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Meeting Day</p>
                <p class="text-lg text-green-700">{{ $cellGroup['meeting_day'] }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Meeting Time</p>
                <p class="text-lg text-green-700">{{ $cellGroup['meeting_time'] }}</p>
            </div>
        </div>
        @if($cellGroup['meeting_location'] !== 'Not Set')
            <div class="mt-3">
                <p class="text-sm font-medium text-gray-700">Location</p>
                <p class="text-lg text-green-700">{{ $cellGroup['meeting_location'] }}</p>
            </div>
        @endif
    </div>

    <!-- Members List -->
    @if($cellGroup['members_count'] > 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-medium text-gray-900">Cell Group Members</h4>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $cellGroup['members_count'] }} members
                </span>
            </div>

            <div class="space-y-3">
                @foreach($cellGroup['members'] as $index => $member)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <!-- Member Number -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                
                                <!-- Member Info -->
                                <div>
                                    <h5 class="text-lg font-semibold text-gray-900">
                                        {{ $member['name'] }}
                                    </h5>
                                    <p class="text-sm text-green-600 font-medium">
                                        {{ $member['role_type'] }}
                                    </p>
                                    @if($member['email'])
                                        <p class="text-sm text-gray-500">
                                            📧 {{ $member['email'] }}
                                        </p>
                                    @endif
                                    @if($member['phone'])
                                        <p class="text-sm text-gray-500">
                                            📱 {{ $member['phone'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Role Badge -->
                            <div>
                                @php
                                    $badgeColor = match($member['role_type']) {
                                        'Network Leader' => 'bg-purple-100 text-purple-800',
                                        'G12 Leader' => 'bg-blue-100 text-blue-800',
                                        'Cell Leader' => 'bg-yellow-100 text-yellow-800',
                                        'Emerging Leader' => 'bg-orange-100 text-orange-800',
                                        'Senior Pastor' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                    {{ $member['role_type'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-gray-50 rounded-lg p-4 border-t">
            <h4 class="font-medium text-gray-900 mb-3">Summary</h4>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $cellGroup['members_count'] }}</p>
                    <p class="text-sm text-gray-600">Total Members</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">1</p>
                    <p class="text-sm text-gray-600">Cell Group</p>
                </div>
            </div>
            
            <!-- Role Distribution -->
            @php
                $roleGroups = collect($cellGroup['members'])->groupBy('role_type');
            @endphp
            @if($roleGroups->count() > 1)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-2">Role Distribution:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($roleGroups as $role => $members)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $role }}: {{ $members->count() }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- No members assigned -->
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Members Assigned</h3>
            <p class="text-sm text-gray-600">
                The {{ $cellGroup['cell_group_name'] }} cell group exists but has no members assigned yet.
                <br>
                Use the "Assign Members" action in the Cell Groups table to add members.
            </p>
        </div>
    @endif
</div>

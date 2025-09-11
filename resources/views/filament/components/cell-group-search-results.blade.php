@php
    $searchResults = session('cell_group_search_results', []);
@endphp

@if(!empty($searchResults))
    @foreach($searchResults as $cellGroup)
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-4">
            {{-- Cell Group Header --}}
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $cellGroup['cell_group_name'] }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Leader:</span>
                        <span class="text-sm text-gray-900">{{ $cellGroup['leader_name'] }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                            {{ $cellGroup['leader_type'] }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Meeting Day:</span>
                        <span class="text-sm text-gray-900">{{ $cellGroup['meeting_day'] }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Meeting Time:</span>
                        <span class="text-sm text-gray-900">{{ $cellGroup['meeting_time'] }}</span>
                    </div>
                </div>
                @if(isset($cellGroup['meeting_location']))
                <div class="mt-2">
                    <span class="text-sm font-medium text-gray-500">Location:</span>
                    <span class="text-sm text-gray-900">{{ $cellGroup['meeting_location'] }}</span>
                </div>
                @endif
            </div>

            {{-- Members Section --}}
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">
                    Members ({{ $cellGroup['members_count'] ?? 0 }})
                </h4>
                
                @if(!empty($cellGroup['members']))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($cellGroup['members'] as $member)
                            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-sm font-semibold text-gray-900">{{ $member['name'] }}</h5>
                                    @php
                                        $badgeColor = match($member['role_type']) {
                                            'Network Leader' => 'bg-red-100 text-red-800',
                                            'G12 Leader' => 'bg-green-100 text-green-800',
                                            'Cell Leader' => 'bg-yellow-100 text-yellow-800',
                                            'Emerging Leader' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                        {{ $member['role_type'] }}
                                    </span>
                                </div>
                                @if(isset($member['email']) && $member['email'])
                                    <p class="text-xs text-gray-600">{{ $member['email'] }}</p>
                                @endif
                                @if(isset($member['phone']) && $member['phone'])
                                    <p class="text-xs text-gray-600">{{ $member['phone'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No members assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">This cell group doesn't have any members yet.</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No search results</h3>
        <p class="mt-1 text-sm text-gray-500">Try searching for a cell group name.</p>
    </div>
@endif

<div class="space-y-6">
    <!-- Header -->
    <div class="text-center border-b pb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            G12 Leaders under {{ $leader->full_name }}
        </h3>
        <p class="text-sm text-gray-600 mt-1">
            Found {{ $g12Leaders->count() }} G12 Leaders
        </p>
    </div>

    @if($g12Leaders->isNotEmpty())
        <!-- Leaders List -->
        <div class="space-y-4">
            @foreach($g12Leaders as $index => $g12Leader)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">
                                    {{ $g12Leader['name'] }}
                                </h4>
                                <p class="text-sm text-green-600 font-medium">
                                    {{ $g12Leader['leader_type'] }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $g12Leader['members_count'] }} members
                            </span>
                        </div>
                    </div>

                    @if($g12Leader['members_under_them']->isNotEmpty())
                        <!-- Members under this G12 Leader -->
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Members under {{ $g12Leader['name'] }}:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($g12Leader['members_under_them'] as $member)
                                    <div class="bg-white rounded-md px-3 py-2 border border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                        @if($member->email)
                                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="bg-gray-50 rounded-lg p-4 border-t">
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $g12Leaders->count() }}</p>
                    <p class="text-sm text-gray-600">G12 Leaders</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $g12Leaders->sum('members_count') }}</p>
                    <p class="text-sm text-gray-600">Total Members</p>
                </div>
            </div>
        </div>
    @else
        <!-- No leaders found -->
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No G12 Leaders Found</h3>
            <p class="text-sm text-gray-600">No G12 Leaders are currently assigned under {{ $leader->full_name }}.</p>
        </div>
    @endif
</div>

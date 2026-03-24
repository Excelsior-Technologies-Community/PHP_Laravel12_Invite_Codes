<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                Invite Codes
            </h2>

            <!-- Generate Button -->
            <form action="{{ route('invites.create') }}" method="POST">
                @csrf
                <button class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow transition">
                    + Generate Invite Code
                </button>
            </form>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    
                    <!-- Table Head -->
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">Max Uses</th>
                            <th class="px-6 py-4">Used</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-100">
                        @forelse($invites as $invite)
                        <tr class="hover:bg-gray-50 transition">

                            <!-- Code -->
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $invite->code }}
                            </td>

                            <!-- Max Uses -->
                            <td class="px-6 py-4">
                                {{ $invite->max_usages ?? '∞' }}
                            </td>

                            <!-- Used -->
                            <td class="px-6 py-4">
                                {{ $invite->uses }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($invite->max_usages && $invite->uses >= $invite->max_usages)
                                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                        Expired
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                        Active
                                    </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                                No invite codes found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</x-app-layout>
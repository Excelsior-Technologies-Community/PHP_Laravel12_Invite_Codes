<x-app-layout>

    <div class="min-h-screen bg-slate-950 py-10">

        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">

                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Invite Code Dashboard
                    </h1>

                    <p class="text-slate-400 mt-2">
                        Manage and generate invite codes
                    </p>
                </div>

                <!-- Generate Button -->
                <form action="{{ route('invites.create') }}" method="POST">
                    @csrf

                    <button
                        class="mt-5 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-lg transition">
                        + Generate Invite
                    </button>
                </form>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="mb-6 bg-green-500/20 border border-green-500 text-green-300 px-5 py-4 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <h3 class="text-slate-400 text-sm">Total Codes</h3>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ $totalCodes }}
                    </p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <h3 class="text-slate-400 text-sm">Active Codes</h3>

                    <p class="text-3xl font-bold text-green-400 mt-2">
                        {{ $activeCodes }}
                    </p>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <h3 class="text-slate-400 text-sm">Used Codes</h3>

                    <p class="text-3xl font-bold text-red-400 mt-2">
                        {{ $usedCodes }}
                    </p>
                </div>

            </div>

            <!-- Search -->
            <div class="mb-6">

                <form method="GET" class="flex flex-col md:flex-row gap-3">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invite code..."
                        class="w-full md:w-96 bg-slate-900 border border-slate-700 text-white rounded-xl px-5 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">

                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl">
                        Search
                    </button>

                    <a href="{{ route('invites.index') }}"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl text-center">
                        Reset
                    </a>

                </form>

            </div>

            <!-- Table -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead class="bg-slate-800 text-slate-300 uppercase text-xs">

                            <tr>
                                <th class="px-6 py-4">Code</th>
                                <th class="px-6 py-4">Uses</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-800">

                            @forelse($invites as $invite)

                                <tr class="hover:bg-slate-800/50 transition">

                                    <!-- Code -->
                                    <td class="px-6 py-5 text-white font-semibold">
                                        {{ $invite->code }}
                                    </td>

                                    <!-- Uses -->
                                    <td class="px-6 py-5 text-slate-300">
                                        {{ $invite->uses }} / {{ $invite->max_usages }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5">

                                        @if($invite->uses >= $invite->max_usages)

                                            <span
                                                class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs font-semibold">
                                                Expired
                                            </span>

                                        @else

                                            <span
                                                class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">
                                                Active
                                            </span>

                                        @endif

                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-5 flex gap-3">

                                        <!-- Copy -->
                                        <button onclick="navigator.clipboard.writeText('{{ $invite->code }}')"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                                            Copy
                                        </button>

                                        <!-- Delete -->
                                        <form action="{{ route('invites.destroy', $invite->id) }}" method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button onclick="return confirm('Delete this invite code?')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="text-center py-10 text-slate-500">
                                        No invite codes found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $invites->links() }}
            </div>

        </div>

    </div>

</x-app-layout>
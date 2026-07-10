<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">🎯 Invite Code Dashboard</h1>
                    <p class="text-slate-400 mt-2">Manage invites, track referrals, and grow your network</p>
                </div>
                <div class="mt-5 md:mt-0 flex gap-3">
                    <a href="{{ route('invites.referrals') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl transition">
                        📊 Referrals
                    </a>
                    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-lg transition">
                        + Generate Invite
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-500/20 border border-green-500 text-green-300 px-5 py-4 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-5 py-4 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Total Codes</h3>
                    <p class="text-2xl font-bold text-white mt-1">{{ $totalCodes }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Active</h3>
                    <p class="text-2xl font-bold text-green-400 mt-1">{{ $activeCodes }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Used</h3>
                    <p class="text-2xl font-bold text-blue-400 mt-1">{{ $usedCodes }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Expired</h3>
                    <p class="text-2xl font-bold text-red-400 mt-1">{{ $expiredCodes }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Referrals</h3>
                    <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $referrals }}</p>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <form method="GET" class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or email..."
                        class="w-full md:w-96 bg-slate-900 border border-slate-700 text-white rounded-xl px-5 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl">Search</button>
                    <a href="{{ route('invites.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl text-center">Reset</a>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-800 text-slate-300 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Code</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Uses</th>
                                <th class="px-6 py-4">Expires</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($invites as $invite)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="px-6 py-5 text-white font-semibold font-mono">{{ $invite->code }}</td>
                                    <td class="px-6 py-5 text-slate-300">{{ $invite->email ?? '—' }}</td>
                                    <td class="px-6 py-5 text-slate-300">{{ $invite->uses }} / {{ $invite->max_uses }}</td>
                                    <td class="px-6 py-5 text-slate-300">
                                        @if($invite->expires_at)
                                            {{ $invite->expires_at->format('M d, Y') }}
                                            @if($invite->expires_at->isPast())
                                                <span class="text-red-400 text-xs block">Expired</span>
                                            @endif
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        {!! $invite->status_badge !!}
                                    </td>
                                    <td class="px-6 py-5 flex gap-2 flex-wrap">
                                        <button onclick="navigator.clipboard.writeText('{{ $invite->code }}')"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm">
                                            📋 Copy
                                        </button>
                                        @if($invite->email)
                                            <a href="{{ route('invites.send-email', $invite->id) }}"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm">
                                                📧 Resend
                                            </a>
                                        @endif
                                        <form action="{{ route('invites.destroy', $invite->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Delete this invite?')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-500">No invite codes found. Generate your first one!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $invites->links() }}</div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-slate-800 rounded-2xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">🎯 Generate Invite</h2>
                <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
            <form method="POST" action="{{ route('invites.create') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Max Uses</label>
                        <input type="number" name="max_uses" value="1" min="1" max="100"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p class="text-xs text-slate-500 mt-1">How many times this code can be used</p>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Expires In (Hours)</label>
                        <input type="number" name="expires_in" placeholder="Leave empty for no expiry" min="1" max="720"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm text-slate-300 mb-1">Email (Optional)</label>
                        <input type="email" name="email" placeholder="friend@example.com"
                            class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="send_email" id="send_email" value="1" class="w-5 h-5">
                        <label for="send_email" class="text-sm text-slate-300">Send email invitation</label>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-semibold transition">
                        🚀 Generate Code
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('createModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    </script>
</x-app-layout>
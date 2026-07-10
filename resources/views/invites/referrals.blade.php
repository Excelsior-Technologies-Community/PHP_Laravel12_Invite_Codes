<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10">
        <div class="max-w-7xl mx-auto px-6">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">📊 Referral Dashboard</h1>
                    <p class="text-slate-400 mt-2">Track your referrals and rewards</p>
                </div>
                <a href="{{ route('invites.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl transition">
                    ← Back to Invites
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Total Referrals</h3>
                    <p class="text-2xl font-bold text-white mt-1">{{ $referralStats['total'] }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Active</h3>
                    <p class="text-2xl font-bold text-green-400 mt-1">{{ $referralStats['active'] }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Pending</h3>
                    <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $referralStats['pending'] }}</p>
                </div>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-slate-400 text-xs uppercase">Points Earned</h3>
                    <p class="text-2xl font-bold text-purple-400 mt-1">{{ $referralStats['points'] }}</p>
                </div>
            </div>

            <!-- Referral Tree -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">🌳 Referral Tree</h3>

                @if($referrals->isEmpty())
                    <div class="text-center py-10 text-slate-500">
                        <div class="text-6xl mb-4">🌱</div>
                        <p>No referrals yet. Share your invite codes!</p>
                        <a href="{{ route('invites.index') }}" class="text-indigo-400 hover:text-indigo-300 mt-2 inline-block">Generate invites →</a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($referrals as $referral)
                            <div class="flex items-center justify-between bg-slate-800/50 rounded-xl p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($referral->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $referral->name }}</p>
                                        <p class="text-sm text-slate-400">{{ $referral->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-400">{{ $referral->created_at->diffForHumans() }}</span>
                                    @if($referral->email_verified_at)
                                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs">✅ Active</span>
                                    @else
                                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs">⏳ Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Points System Info -->
            <div class="mt-6 bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <h4 class="text-sm font-semibold text-slate-400 mb-2">💡 How Referral Points Work</h4>
                <ul class="text-sm text-slate-300 space-y-1">
                    <li>• <span class="text-emerald-400">+10 points</span> for each new user who signs up using your invite</li>
                    <li>• <span class="text-emerald-400">+5 points</span> when they verify their email</li>
                    <li>• <span class="text-yellow-400">🏆 Bonus points</span> for reaching referral milestones</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
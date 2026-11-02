@extends('layouts.app')

@section('title', 'Admin — User Management & Account Governance')

@section('content')
<div class="bg-slate-50 min-h-screen py-8 sm:py-12">
    <div class="agri-container max-w-7xl mx-auto px-4 sm:px-6 space-y-6">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.index') }}" class="hover:text-[#0c365b] font-medium">Admin Portal</a>
                    <span>/</span>
                    <span class="text-slate-800 font-bold">User Management</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0c365b] tracking-tight">
                    User Accounts & Account Governance
                </h1>
            </div>
            <a href="{{ route('admin.index') }}" class="text-xs font-bold text-[#0c365b] hover:underline self-start sm:self-auto">
                ← Return to Admin Overview
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Role Filter Tabs -->
        <div class="bg-white rounded-2xl p-2 border border-slate-200/90 shadow-xs flex flex-wrap gap-2">
            <a href="{{ route('admin.users') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ !request('role') ? 'bg-[#0c365b] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                All Roles ({{ $roleCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', ['role' => 'farmer']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('role') === 'farmer' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🌾 Farmers ({{ $roleCounts['farmer'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', ['role' => 'supplier']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ in_array(request('role'), ['supplier', 'distributor']) ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🏢 Suppliers ({{ $roleCounts['supplier'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', ['role' => 'buyer']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('role') === 'buyer' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🛒 Buyers ({{ $roleCounts['buyer'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', ['role' => 'fpo']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ in_array(request('role'), ['fpo', 'cooperative']) ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🤝 FPO / Coops ({{ $roleCounts['fpo'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', ['role' => 'admin']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request('role') === 'admin' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🛡️ Admins ({{ $roleCounts['admin'] ?? 0 }})
            </a>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="{{ route('admin.users') }}" method="GET" class="w-full sm:w-80">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0c365b] outline-none">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                    </div>
                </form>
                <div class="text-xs text-slate-500 font-semibold self-end sm:self-auto">
                    Displaying {{ $users->total() }} user accounts
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Contact Details</th>
                            <th class="py-3 px-4">District / State</th>
                            <th class="py-3 px-4">Account Status</th>
                            <th class="py-3 px-4">Registered</th>
                            <th class="py-3 px-4 text-right">Account Governance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="text-sm">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-normal">ID: #{{ $user->id }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeColor = match($user->role) {
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'farmer' => 'bg-emerald-100 text-emerald-800',
                                            'buyer' => 'bg-blue-100 text-blue-800',
                                            'supplier', 'distributor' => 'bg-amber-100 text-amber-800',
                                            'fpo', 'cooperative' => 'bg-teal-100 text-teal-800',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase tracking-wider {{ $badgeColor }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <div>{{ $user->email }}</div>
                                    <div class="text-slate-400 font-normal">{{ $user->phone ?? '—' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $user->district ?? 'Gujarat' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($user->status === 'active')
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Active</span>
                                    @elseif($user->status === 'suspended')
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">Suspended</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] capitalize">{{ $user->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if(!$user->isAdmin() || $user->id !== auth()->id())
                                        <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Change status of user {{ $user->name }}?');">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold cursor-pointer {{ $user->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                                                {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">Current Session</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No users found matching the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

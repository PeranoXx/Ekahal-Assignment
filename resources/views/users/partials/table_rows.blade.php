@forelse($users as $user)
@php
    $initials = collect(explode(' ', $user->name))
        ->map(fn($segment) => mb_substr($segment, 0, 1))
        ->take(2)
        ->join('');
@endphp
<tr class="hover:bg-surface-container-low transition-colors group {{ $user->trashed() ? 'opacity-60' : '' }}">
    <!-- <td class="py-md px-lg">
        <input class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20" type="checkbox"/>
    </td> -->
    <td class="py-md px-lg font-medium text-on-surface">
        <div class="flex items-center gap-md">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-outline-variant bg-inverse-surface shrink-0 flex items-center justify-center text-primary-fixed-dim font-title-md">
                {{ $initials }}
            </div>
            <div class="font-title-md text-[16px] leading-tight text-on-surface">
                {{ $user->name }}
                @if($user->id === Auth::id())
                    <span class="ml-1 text-[10px] text-on-surface-variant font-normal">(You)</span>
                @endif
            </div>
        </div>
    </td>
    <td class="py-md px-lg text-on-surface-variant">
        {{ $user->email }}
    </td>
    <td class="py-md px-lg text-on-surface-variant">{{ $user->hasRole('Admin') ? 'Admin' : 'User' }}</td>
    <td class="py-md px-lg">
        @if ($user->trashed())
            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-error-container text-on-error-container font-label-caps text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                DELETED
            </span>
        @else
            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-[#E0F2E9] text-[#1D7A46] font-label-caps text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#1D7A46]"></span>
                ACTIVE
            </span>
        @endif
    </td>
    <td class="py-md px-lg text-on-surface-variant">
        {{ $user->trashed() ? 'N/A' : $user->updated_at->diffForHumans() }}
    </td>
    <td class="py-md px-lg text-right">
        <div class="flex items-center justify-end gap-2 min-h-[24px]">
            @if(!$user->trashed())
                @can('user-update')
                <a href="{{ route('users.edit', $user) }}" class="p-xs text-on-surface-variant hover:text-primary transition-colors flex items-center" title="Edit">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                </a>
                @endcan

                @can('user-delete')
                    @if($user->id !== Auth::id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-xs text-error hover:opacity-85 flex items-center bg-transparent border-0 cursor-pointer" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </form>
                    @endif
                @endcan
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="p-8 text-center text-on-surface-variant font-body-sm">
        No users found.
    </td>
</tr>
@endforelse

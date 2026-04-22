<div>
    <div class="page-header">
        <h1 class="page-title">Usuarios del Sistema</h1>
        @can('create users')
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Usuario
        </button>
        @endcan
    </div>

    <div class="card mb-4"><div class="card-body py-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar usuario..." class="input max-w-xs">
    </div></div>

    <div class="card">
        <div class="table-wrapper rounded-2xl">
            <table class="table">
                <thead><tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último acceso</th>
                    <th class="text-right">Acciones</th>
                </tr></thead>
                <tbody>
                @forelse($users as $user)
                <tr wire:key="user-{{ $user->id }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 text-xs font-bold uppercase">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="badge-blue text-xs">Yo</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-xs text-gray-500">{{ $user->email }}</td>
                    <td>
                        @foreach($user->getRoleNames() as $role)
                            <span class="{{ match($role) { 'admin'=>'badge-purple','cajero'=>'badge-blue',default=>'badge-gray' } }} text-xs capitalize">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="{{ $user->is_active ? 'badge-green' : 'badge-red' }} text-xs">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-xs text-gray-400">{{ $user->updated_at->diffForHumans() }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            @can('edit users')
                            <button wire:click="edit({{ $user->id }})" class="btn-icon btn-sm text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @endcan
                            @if($user->id !== auth()->id())
                            @can('edit users')
                            <button wire:click="toggleStatus({{ $user->id }})"
                                    class="btn-icon btn-sm {{ $user->is_active ? 'text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/30' : 'text-green-500 hover:bg-green-50 dark:hover:bg-green-900/30' }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                            </button>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-sm text-gray-400">No se encontraron usuarios</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="card-footer">{{ $users->links() }}</div>@endif
    </div>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="modal-overlay">
        <div class="modal modal-sm p-6" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="label">Nombre</label>
                    <input wire:model="name" type="text" class="input">
                    @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Email</label>
                    <input wire:model="email" type="email" class="input">
                    @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Contraseña {{ $editingId ? '(dejar vacío para no cambiar)' : '' }}</label>
                    <input wire:model="password" type="password" class="input" placeholder="{{ $editingId ? 'Nueva contraseña...' : 'Mínimo 8 caracteres' }}">
                    @error('password') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Rol</label>
                    <select wire:model="role" class="input">
                        <option value="admin">Administrador</option>
                        <option value="cajero">Cajero</option>
                        <option value="mecanico">Mecánico</option>
                    </select>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Usuario activo</span>
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn-primary flex-1" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingId ? 'Actualizar' : 'Crear Usuario' }}</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

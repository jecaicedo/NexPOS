<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;

    public string $search  = '';
    public int    $perPage = 15;

    public bool    $showForm     = false;
    public bool    $showConfirm  = false;
    public ?int    $editingId    = null;
    public ?int    $togglingId   = null;

    // Form
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $role     = 'cajero';
    public bool   $is_active= true;

    public function updatingSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $u = User::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $u->name;
        $this->email     = $u->email;
        $this->password  = '';
        $this->role      = $u->getRoleNames()->first() ?? 'cajero';
        $this->is_active = $u->is_active;
        $this->showForm  = true;
    }

    public function save(): void
    {
        $rules = [
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'role'     => 'required|in:admin,cajero,mecanico',
            'is_active'=> 'boolean',
        ];

        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:8';
        } elseif ($this->password) {
            $rules['password'] = 'string|min:8';
        }

        $this->validate($rules);

        $data = ['name' => $this->name, 'email' => $this->email, 'is_active' => $this->is_active];
        if ($this->password) $data['password'] = $this->password;

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        $user->syncRoles([$this->role]);

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Usuario guardado.');
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', type: 'warning', message: 'No puedes desactivar tu propia cuenta.');
            return;
        }
        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('toast', type: 'success', message: 'Estado actualizado.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = $this->email = $this->password = '';
        $this->role = 'cajero';
        $this->is_active = true;
    }

    public function render()
    {
        $users = User::withoutTrashed()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->with('roles')
            ->paginate($this->perPage);

        return view('livewire.users.user-list', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }
}

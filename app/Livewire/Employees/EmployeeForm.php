<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use App\Models\User;
use Livewire\Component;

class EmployeeForm extends Component
{
    public ?int   $employeeId       = null;
    public ?int   $user_id          = null;
    public string $name             = '';
    public string $document_number  = '';
    public string $phone            = '';
    public string $email            = '';
    public string $specialty        = '';
    public float  $labor_rate       = 0;
    public bool   $is_active        = true;
    public string $notes            = '';

    public function mount(?int $employeeId = null): void
    {
        $this->employeeId = $employeeId;
        if ($employeeId) {
            $e = Employee::findOrFail($employeeId);
            $this->user_id         = $e->user_id;
            $this->name            = $e->name;
            $this->document_number = $e->document_number ?? '';
            $this->phone           = $e->phone ?? '';
            $this->email           = $e->email ?? '';
            $this->specialty       = $e->specialty ?? '';
            $this->labor_rate      = (float) $e->labor_rate;
            $this->is_active       = $e->is_active;
            $this->notes           = $e->notes ?? '';
        }
    }

    public function getAvailableUsersProperty()
    {
        return User::whereDoesntHave('employee', fn($q) => $q->where('id', '!=', $this->employeeId ?? 0))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function save(): void
    {
        $this->validate([
            'name'            => 'required|string|max:150',
            'document_number' => 'nullable|string|max:30|unique:employees,document_number,' . ($this->employeeId ?? 'NULL'),
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:150',
            'specialty'       => 'nullable|string|max:100',
            'labor_rate'      => 'required|numeric|min:0',
        ]);

        $data = [
            'user_id'         => $this->user_id ?: null,
            'name'            => $this->name,
            'document_number' => $this->document_number ?: null,
            'phone'           => $this->phone ?: null,
            'email'           => $this->email ?: null,
            'specialty'       => $this->specialty ?: null,
            'labor_rate'      => $this->labor_rate,
            'is_active'       => $this->is_active,
            'notes'           => $this->notes ?: null,
        ];

        $this->employeeId
            ? Employee::findOrFail($this->employeeId)->update($data)
            : Employee::create($data);

        $this->dispatch('employee-saved');
    }

    public function cancel(): void { $this->dispatch('close-modal'); }

    public function render()
    {
        return view('livewire.employees.employee-form', [
            'availableUsers' => $this->availableUsers,
        ]);
    }
}

<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeList extends Component
{
    use WithPagination;

    public string $search  = '';
    public int    $perPage = 15;

    public bool $showForm        = false;
    public bool $showDeleteModal = false;
    public ?int $editingId       = null;
    public ?int $deletingId      = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function create(): void { $this->editingId = null; $this->showForm = true; }
    public function edit(int $id): void { $this->editingId = $id; $this->showForm = true; }

    #[On('employee-saved')]
    public function onSaved(): void { $this->showForm = false; $this->dispatch('toast', type: 'success', message: 'Empleado guardado.'); }

    #[On('close-modal')]
    public function onClose(): void { $this->showForm = false; }

    public function confirmDelete(int $id): void { $this->deletingId = $id; $this->showDeleteModal = true; }

    public function delete(): void
    {
        Employee::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->dispatch('toast', type: 'success', message: 'Empleado eliminado.');
    }

    public function render()
    {
        $employees = Employee::withCount('workshopJobs')->with('user')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('specialty', 'like', "%{$this->search}%")
                  ->orWhere('document_number', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.employees.employee-list', compact('employees'));
    }
}

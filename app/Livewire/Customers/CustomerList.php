<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $sortField = 'name';
    public string $sortDir   = 'asc';
    public int    $perPage   = 15;

    public bool $showForm        = false;
    public bool $showDeleteModal = false;
    public ?int $editingId       = null;
    public ?int $deletingId      = null;

    public function updatingSearch(): void { $this->resetPage(); }

    public function sort(string $field): void
    {
        $this->sortDir = ($this->sortField === $field && $this->sortDir === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function createCustomer(): void
    {
        $this->editingId = null;
        $this->showForm  = true;
    }

    public function editCustomer(int $id): void
    {
        $this->editingId = $id;
        $this->showForm  = true;
    }

    #[On('customer-saved')]
    public function onCustomerSaved(): void
    {
        $this->showForm = false;
        $this->dispatch('toast', type: 'success', message: 'Cliente guardado.');
    }

    #[On('close-modal')]
    public function onCloseModal(): void
    {
        $this->showForm = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCustomer(): void
    {
        Customer::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->dispatch('toast', type: 'success', message: 'Cliente eliminado.');
    }

    public function render()
    {
        $query = Customer::where('is_generic', false)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('document_number', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->withCount('sales')
            ->orderBy($this->sortField, $this->sortDir);

        return view('livewire.customers.customer-list', [
            'customers' => $query->paginate($this->perPage),
            'total'     => Customer::where('is_generic', false)->count(),
        ]);
    }
}

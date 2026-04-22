<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
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

    #[On('category-saved')]
    public function onSaved(): void
    {
        $this->showForm = false;
        $this->dispatch('toast', type: 'success', message: 'Categoría guardada.');
    }

    #[On('close-modal')]
    public function onClose(): void { $this->showForm = false; }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $category = Category::withCount('products')->findOrFail($this->deletingId);

        if ($category->products_count > 0) {
            $this->showDeleteModal = false;
            $this->dispatch('toast', type: 'error', message: 'No se puede eliminar: tiene productos asignados.');
            return;
        }

        $category->delete();
        $this->showDeleteModal = false;
        $this->dispatch('toast', type: 'success', message: 'Categoría eliminada.');
    }

    public function toggleActive(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        $this->dispatch('toast', type: 'success', message: 'Estado actualizado.');
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.categories.category-list', compact('categories'));
    }
}

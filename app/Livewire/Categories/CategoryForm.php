<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?int   $categoryId  = null;
    public string $name        = '';
    public string $description = '';
    public string $color       = '#6366f1';
    public bool   $is_active   = true;

    public function mount(?int $categoryId = null): void
    {
        $this->categoryId = $categoryId;
        if ($categoryId) {
            $c = Category::findOrFail($categoryId);
            $this->name        = $c->name;
            $this->description = $c->description ?? '';
            $this->color       = $c->color ?? '#6366f1';
            $this->is_active   = $c->is_active;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . ($this->categoryId ?? 'NULL'),
            'description' => 'nullable|string|max:255',
            'color'       => 'required|string|max:7',
        ]);

        $data = [
            'name'        => $this->name,
            'description' => $this->description ?: null,
            'color'       => $this->color,
            'is_active'   => $this->is_active,
        ];

        $this->categoryId
            ? Category::findOrFail($this->categoryId)->update($data)
            : Category::create($data);

        $this->dispatch('category-saved');
    }

    public function cancel(): void { $this->dispatch('close-modal'); }

    public function render() { return view('livewire.categories.category-form'); }
}

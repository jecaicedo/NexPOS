<?php

namespace App\Livewire\Workshop;

use App\Models\WorkshopJob;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class WorkshopList extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $status  = '';
    public int    $perPage = 15;

    public bool $showForm        = false;
    public bool $showDeleteModal = false;
    public ?int $editingId       = null;
    public ?int $deletingId      = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function create(): void { $this->editingId = null; $this->showForm = true; }
    public function edit(int $id): void { $this->editingId = $id; $this->showForm = true; }

    #[On('job-saved')]
    public function onSaved(): void { $this->showForm = false; $this->dispatch('toast', type: 'success', message: 'Trabajo guardado.'); }

    #[On('close-modal')]
    public function onClose(): void { $this->showForm = false; }

    public function confirmDelete(int $id): void { $this->deletingId = $id; $this->showDeleteModal = true; }

    public function delete(): void
    {
        WorkshopJob::findOrFail($this->deletingId)->delete();
        $this->showDeleteModal = false;
        $this->dispatch('toast', type: 'success', message: 'Trabajo eliminado.');
    }

    public function updateStatus(int $id, string $status): void
    {
        $job = WorkshopJob::findOrFail($id);
        $data = ['status' => $status];

        if ($status === 'in_progress' && !$job->started_at) {
            $data['started_at'] = now();
        }
        if ($status === 'completed' && !$job->completed_at) {
            $data['completed_at'] = now();
        }

        $job->update($data);
        $this->dispatch('toast', type: 'success', message: 'Estado actualizado.');
    }

    public function render()
    {
        $jobs = WorkshopJob::with(['customer', 'employee'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('job_number', 'like', "%{$this->search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate($this->perPage);

        $counts = WorkshopJob::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('livewire.workshop.workshop-list', compact('jobs', 'counts'));
    }
}

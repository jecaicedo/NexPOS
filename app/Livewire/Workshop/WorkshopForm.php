<?php

namespace App\Livewire\Workshop;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\WorkshopJob;
use App\Models\WorkshopJobPart;
use Livewire\Component;

class WorkshopForm extends Component
{
    public ?int    $jobId          = null;
    public ?int    $customer_id    = null;
    public ?int    $employee_id    = null;
    public string  $vehicle_brand  = '';
    public string  $vehicle_model  = '';
    public string  $vehicle_plate  = '';
    public string  $vehicle_year   = '';
    public string  $description    = '';
    public string  $diagnosis      = '';
    public string  $status         = 'pending';
    public float   $labor_cost     = 0;
    public string  $notes          = '';

    // Parts
    public array  $parts           = [];
    public string $partSearch      = '';
    public array  $partResults     = [];

    public function mount(?int $jobId = null): void
    {
        $this->jobId = $jobId;

        if ($jobId) {
            $job = WorkshopJob::with('parts')->findOrFail($jobId);
            $this->customer_id   = $job->customer_id;
            $this->employee_id   = $job->employee_id;
            $this->vehicle_brand = $job->vehicle_brand ?? '';
            $this->vehicle_model = $job->vehicle_model ?? '';
            $this->vehicle_plate = $job->vehicle_plate ?? '';
            $this->vehicle_year  = $job->vehicle_year ?? '';
            $this->description   = $job->description;
            $this->diagnosis     = $job->diagnosis ?? '';
            $this->status        = $job->status;
            $this->labor_cost    = (float) $job->labor_cost;
            $this->notes         = $job->notes ?? '';

            foreach ($job->parts as $part) {
                $this->parts[] = [
                    'product_id' => $part->product_id,
                    'name'       => $part->name,
                    'price'      => (float) $part->price,
                    'quantity'   => $part->quantity,
                    'subtotal'   => (float) $part->subtotal,
                ];
            }
        }
    }

    public function updatedPartSearch(): void
    {
        if (strlen($this->partSearch) < 2) { $this->partResults = []; return; }
        $this->partResults = Product::where('is_active', true)
            ->where('name', 'like', "%{$this->partSearch}%")
            ->limit(8)
            ->get(['id', 'name', 'sale_price'])->toArray();
    }

    public function addPart(int $productId): void
    {
        $p = Product::find($productId);
        if (!$p) return;
        $this->parts[] = ['product_id' => $p->id, 'name' => $p->name, 'price' => (float)$p->sale_price, 'quantity' => 1, 'subtotal' => (float)$p->sale_price];
        $this->partSearch = '';
        $this->partResults = [];
    }

    public function removePart(int $index): void
    {
        array_splice($this->parts, $index, 1);
    }

    public function updatePartQty(int $index, int $qty): void
    {
        if ($qty <= 0) { array_splice($this->parts, $index, 1); return; }
        $this->parts[$index]['quantity'] = $qty;
        $this->parts[$index]['subtotal'] = round($this->parts[$index]['price'] * $qty, 2);
    }

    public function getPartsCostProperty(): float
    {
        return array_sum(array_column($this->parts, 'subtotal'));
    }

    public function getTotalProperty(): float
    {
        return $this->labor_cost + $this->parts_cost;
    }

    public function save(): void
    {
        $this->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            'description' => 'required|string|min:5',
            'labor_cost'  => 'required|numeric|min:0',
            'status'      => 'required|in:pending,in_progress,completed,delivered,cancelled',
        ]);

        $data = [
            'customer_id'   => $this->customer_id,
            'employee_id'   => $this->employee_id,
            'user_id'       => auth()->id(),
            'vehicle_brand' => $this->vehicle_brand ?: null,
            'vehicle_model' => $this->vehicle_model ?: null,
            'vehicle_plate' => $this->vehicle_plate ?: null,
            'vehicle_year'  => $this->vehicle_year ?: null,
            'description'   => $this->description,
            'diagnosis'     => $this->diagnosis ?: null,
            'status'        => $this->status,
            'labor_cost'    => $this->labor_cost,
            'parts_cost'    => $this->parts_cost,
            'total'         => $this->total,
            'notes'         => $this->notes ?: null,
        ];

        if ($this->jobId) {
            $job = WorkshopJob::findOrFail($this->jobId);
            $job->update($data);
            $job->parts()->delete();
        } else {
            $data['job_number'] = WorkshopJob::generateJobNumber();
            $job = WorkshopJob::create($data);
        }

        foreach ($this->parts as $part) {
            WorkshopJobPart::create([
                'workshop_job_id' => $job->id,
                'product_id'      => $part['product_id'],
                'name'            => $part['name'],
                'price'           => $part['price'],
                'quantity'        => $part['quantity'],
                'subtotal'        => $part['subtotal'],
            ]);
        }

        $this->dispatch('job-saved');
    }

    public function cancel(): void { $this->dispatch('close-modal'); }

    public function render()
    {
        return view('livewire.workshop.workshop-form', [
            'customers' => Customer::where('is_active', true)->where('is_generic', false)->orderBy('name')->get(),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}

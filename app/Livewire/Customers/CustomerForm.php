<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class CustomerForm extends Component
{
    public ?int $customerId = null;

    public string  $name            = '';
    public string  $document_type   = 'CC';
    public string  $document_number = '';
    public string  $email           = '';
    public string  $phone           = '';
    public string  $address         = '';
    public string  $city            = '';
    public bool    $is_active       = true;
    public string  $notes           = '';

    public function mount(?int $customerId = null): void
    {
        $this->customerId = $customerId;

        if ($customerId) {
            $c = Customer::findOrFail($customerId);
            $this->name            = $c->name;
            $this->document_type   = $c->document_type;
            $this->document_number = $c->document_number ?? '';
            $this->email           = $c->email ?? '';
            $this->phone           = $c->phone ?? '';
            $this->address         = $c->address ?? '';
            $this->city            = $c->city ?? '';
            $this->is_active       = $c->is_active;
            $this->notes           = $c->notes ?? '';
        }
    }

    public function save(): void
    {
        $uniqueRule = 'nullable|string|max:30|unique:customers,document_number,' . ($this->customerId ?? 'NULL');

        $this->validate([
            'name'            => 'required|string|max:150',
            'document_type'   => 'required|in:CC,NIT,CE,Pasaporte',
            'document_number' => $uniqueRule,
            'email'           => 'nullable|email|max:150',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:200',
            'city'            => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $data = [
            'name'            => $this->name,
            'document_type'   => $this->document_type,
            'document_number' => $this->document_number ?: null,
            'email'           => $this->email ?: null,
            'phone'           => $this->phone ?: null,
            'address'         => $this->address ?: null,
            'city'            => $this->city ?: null,
            'is_active'       => $this->is_active,
            'notes'           => $this->notes ?: null,
        ];

        if ($this->customerId) {
            Customer::findOrFail($this->customerId)->update($data);
        } else {
            Customer::create($data);
        }

        $this->dispatch('customer-saved');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}

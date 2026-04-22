<?php

namespace App\Livewire\Settings;

use App\Models\BusinessSetting;
use Livewire\Component;
use Livewire\WithFileUploads;

class BusinessSettings extends Component
{
    use WithFileUploads;

    public string $business_name    = '';
    public string $business_address = '';
    public string $business_phone   = '';
    public string $business_email   = '';
    public string $business_nit     = '';
    public string $business_city    = '';
    public string $currency_symbol  = '$';
    public string $currency_code    = 'COP';
    public bool   $tax_enabled      = false;
    public string $tax_percent      = '19';
    public string $tax_name         = 'IVA';
    public string $invoice_footer   = '';
    public string $thermal_width    = '80';
    public mixed  $logo             = null;

    public function mount(): void
    {
        $this->business_name    = BusinessSetting::get('business_name', '');
        $this->business_address = BusinessSetting::get('business_address', '');
        $this->business_phone   = BusinessSetting::get('business_phone', '');
        $this->business_email   = BusinessSetting::get('business_email', '');
        $this->business_nit     = BusinessSetting::get('business_nit', '');
        $this->business_city    = BusinessSetting::get('business_city', '');
        $this->currency_symbol  = BusinessSetting::get('currency_symbol', '$');
        $this->currency_code    = BusinessSetting::get('currency_code', 'COP');
        $this->tax_enabled      = BusinessSetting::get('tax_enabled', '0') === '1';
        $this->tax_percent      = BusinessSetting::get('tax_percent', '19');
        $this->tax_name         = BusinessSetting::get('tax_name', 'IVA');
        $this->invoice_footer   = BusinessSetting::get('invoice_footer', '');
        $this->thermal_width    = BusinessSetting::get('thermal_width', '80');
    }

    public function save(): void
    {
        $this->validate([
            'business_name'   => 'required|string|max:200',
            'business_address'=> 'nullable|string|max:200',
            'business_phone'  => 'nullable|string|max:30',
            'business_email'  => 'nullable|email|max:150',
            'business_nit'    => 'nullable|string|max:50',
            'tax_percent'     => 'required|numeric|min:0|max:100',
            'thermal_width'   => 'required|in:58,80',
            'logo'            => 'nullable|image|max:1024',
        ]);

        $settings = [
            'business_name'    => $this->business_name,
            'business_address' => $this->business_address,
            'business_phone'   => $this->business_phone,
            'business_email'   => $this->business_email,
            'business_nit'     => $this->business_nit,
            'business_city'    => $this->business_city,
            'currency_symbol'  => $this->currency_symbol,
            'currency_code'    => $this->currency_code,
            'tax_enabled'      => $this->tax_enabled ? '1' : '0',
            'tax_percent'      => $this->tax_percent,
            'tax_name'         => $this->tax_name,
            'invoice_footer'   => $this->invoice_footer,
            'thermal_width'    => $this->thermal_width,
        ];

        if ($this->logo) {
            $path = $this->logo->store('logo', 'public');
            $settings['business_logo'] = $path;
        }

        BusinessSetting::setMany($settings);

        $this->dispatch('toast', type: 'success', message: 'Configuración guardada correctamente.');
    }

    public function render()
    {
        return view('livewire.settings.business-settings', [
            'existingLogo' => BusinessSetting::get('business_logo'),
        ]);
    }
}

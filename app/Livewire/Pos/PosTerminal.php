<?php

namespace App\Livewire\Pos;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosTerminal extends Component
{
    // Cart items: [product_id, name, price, quantity, discount, subtotal]
    public array  $cart           = [];
    public string $productSearch  = '';
    public array  $searchResults  = [];

    // Customer
    public ?int   $customerId     = null;
    public string $customerSearch = '';
    public array  $customerResults= [];
    public ?array $selectedCustomer = null;

    // Payment
    public string $paymentMethod  = 'cash';
    public float  $amountPaid     = 0;
    public float  $discount       = 0;
    public string $notes          = '';

    // Sale type
    public string $saleType = 'product'; // product | service | mixed

    // Computed
    public float $subtotal      = 0;
    public float $taxAmount     = 0;
    public float $total         = 0;
    public float $changeAmount  = 0;

    // Modals
    public bool $showPaymentModal    = false;
    public bool $showSuccessModal    = false;
    public ?int $lastSaleId          = null;

    // Tax config
    public float  $taxPercent = 0;
    public bool   $taxEnabled = false;

    public function mount(): void
    {
        $this->taxPercent = (float) BusinessSetting::get('tax_percent', 19);
        $this->taxEnabled = BusinessSetting::get('tax_enabled', '0') === '1';

        // Pre-select generic customer
        $generic = Customer::where('is_generic', true)->first();
        if ($generic) {
            $this->customerId      = $generic->id;
            $this->selectedCustomer = [
                'id'   => $generic->id,
                'name' => $generic->name,
            ];
        }
    }

    // ── Product Search ──────────────────────────────────────────────

    public function updatedProductSearch(): void
    {
        if (strlen($this->productSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->productSearch}%")
                  ->orWhere('sku', 'like', "%{$this->productSearch}%")
                  ->orWhere('barcode', $this->productSearch);
            })
            ->limit(10)
            ->get(['id', 'name', 'sku', 'sale_price', 'stock', 'track_stock', 'unit'])
            ->toArray();
    }

    public function addProduct(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_active) return;

        $key = "p_{$productId}";

        if (isset($this->cart[$key])) {
            $item = $this->cart[$key];
            $item['quantity']++;
            $this->cart[$key] = $item;
        } else {
            $this->cart[$key] = [
                'product_id' => $productId,
                'item_type'  => 'product',
                'name'       => $product->name,
                'price'      => (float) $product->sale_price,
                'quantity'   => 1,
                'discount'   => 0,
                'unit'       => $product->unit,
                'stock'      => $product->stock,
                'track_stock'=> $product->track_stock,
            ];
        }

        $this->recalc();
        $this->productSearch  = '';
        $this->searchResults  = [];
    }

    public function addService(string $name, float $price): void
    {
        if (!$name || $price <= 0) return;

        $key = 'svc_' . time();
        $this->cart[$key] = [
            'product_id' => null,
            'item_type'  => 'service',
            'name'       => $name,
            'price'      => $price,
            'quantity'   => 1,
            'discount'   => 0,
            'unit'       => 'servicio',
            'stock'      => null,
            'track_stock'=> false,
        ];

        $this->recalc();
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
        $this->recalc();
    }

    public function updateQuantity(string $key, int $qty): void
    {
        if ($qty <= 0) {
            unset($this->cart[$key]);
        } else {
            $this->cart[$key]['quantity'] = $qty;
        }
        $this->recalc();
    }

    public function updateItemDiscount(string $key, float $discount): void
    {
        $this->cart[$key]['discount'] = max(0, $discount);
        $this->recalc();
    }

    public function clearCart(): void
    {
        $this->cart        = [];
        $this->discount    = 0;
        $this->amountPaid  = 0;
        $this->changeAmount= 0;
        $this->recalc();
    }

    // ── Customer Search ─────────────────────────────────────────────

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerResults = [];
            return;
        }

        $this->customerResults = Customer::where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->customerSearch}%")
                  ->orWhere('document_number', 'like', "%{$this->customerSearch}%")
                  ->orWhere('phone', 'like', "%{$this->customerSearch}%");
            })
            ->limit(8)
            ->get(['id', 'name', 'document_number', 'document_type', 'phone'])
            ->toArray();
    }

    public function selectCustomer(int $id): void
    {
        $c = Customer::find($id);
        if (!$c) return;

        $this->customerId       = $c->id;
        $this->selectedCustomer = ['id' => $c->id, 'name' => $c->name, 'document_number' => $c->document_number];
        $this->customerSearch   = '';
        $this->customerResults  = [];
    }

    public function clearCustomer(): void
    {
        $generic = Customer::where('is_generic', true)->first();
        $this->customerId      = $generic?->id;
        $this->selectedCustomer = $generic ? ['id' => $generic->id, 'name' => $generic->name] : null;
    }

    // ── Calculations ────────────────────────────────────────────────

    public function recalc(): void
    {
        $sub = 0;
        foreach ($this->cart as $key => $item) {
            if (! isset($item['price'], $item['quantity'], $item['discount'])) {
                continue;
            }
            $subtotal = max(0, round(($item['price'] * $item['quantity']) - $item['discount'], 2));
            $this->cart[$key]['subtotal'] = $subtotal;
            $sub += $subtotal;
        }

        $this->subtotal    = max(0, $sub - $this->discount);
        $this->taxAmount   = $this->taxEnabled ? round($this->subtotal * ($this->taxPercent / 100), 2) : 0;
        $this->total       = $this->subtotal + $this->taxAmount;
        $this->changeAmount= max(0, $this->amountPaid - $this->total);
    }

    public function updatedAmountPaid(): void
    {
        $this->changeAmount = max(0, $this->amountPaid - $this->total);
    }

    public function updatedDiscount(): void
    {
        $this->recalc();
    }

    // ── Checkout ────────────────────────────────────────────────────

    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', type: 'warning', message: 'El carrito está vacío.');
            return;
        }
        $this->amountPaid = $this->total;
        $this->recalc();
        $this->showPaymentModal = true;
    }

    public function processPayment(): void
    {
        $this->validate([
            'paymentMethod' => 'required|in:cash,card,transfer,mixed',
            'amountPaid'    => 'required|numeric|min:0',
        ]);

        if ($this->paymentMethod === 'cash' && $this->amountPaid < $this->total) {
            $this->addError('amountPaid', 'El monto pagado es insuficiente.');
            return;
        }

        DB::transaction(function () {
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'customer_id'    => $this->customerId,
                'user_id'        => auth()->id(),
                'sale_type'      => $this->saleType,
                'status'         => 'completed',
                'payment_method' => $this->paymentMethod,
                'subtotal'       => $this->subtotal,
                'tax_percent'    => $this->taxEnabled ? $this->taxPercent : 0,
                'tax_amount'     => $this->taxAmount,
                'discount'       => $this->discount,
                'total'          => $this->total,
                'amount_paid'    => $this->amountPaid,
                'change_amount'  => $this->changeAmount,
                'notes'          => $this->notes,
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'item_type'  => $item['item_type'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'discount'   => $item['discount'],
                    'subtotal'   => $item['subtotal'],
                ]);

                // Descontar stock
                if ($item['product_id'] && $item['track_stock']) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $before  = $product->stock;
                        $product->stock = max(0, $product->stock - $item['quantity']);
                        $product->save();

                        StockMovement::create([
                            'product_id'   => $product->id,
                            'user_id'      => auth()->id(),
                            'type'         => 'out',
                            'quantity'     => $item['quantity'],
                            'stock_before' => $before,
                            'stock_after'  => $product->stock,
                            'reference'    => $sale->invoice_number,
                        ]);
                    }
                }
            }

            ActivityLog::record('sale_created', "Venta {$sale->invoice_number} por \${$sale->total}", $sale);

            $this->lastSaleId = $sale->id;
        });

        $this->showPaymentModal = false;
        $this->showSuccessModal = true;
        $this->clearCart();
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->lastSaleId       = null;
    }

    public function render()
    {
        return view('livewire.pos.pos-terminal');
    }
}

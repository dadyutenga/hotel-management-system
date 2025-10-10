<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FolioController extends Controller
{
    /**
     * Display the specified folio
     */
    public function show(Folio $folio)
    {
        $user = Auth::user();
        
        // Verify tenant ownership through reservation
        if ($folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this folio.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to view folios.');
        }

        $folio->load([
            'reservation.guest',
            'reservation.property',
            'reservation.reservationRooms.room',
            'folioItems',
            'payments.creator',
            'invoices'
        ]);

        // Calculate totals
        $totalCharges = $folio->folioItems()->where('amount', '>', 0)->sum('amount');
        $totalPayments = $folio->payments()->where('status', 'COMPLETED')->sum('amount');
        $balance = $totalCharges - $totalPayments;

        return view('Users.tenant.folios.show', compact('folio', 'totalCharges', 'totalPayments', 'balance'));
    }

    /**
     * Add a charge to the folio
     */
    public function addCharge(Request $request, Folio $folio)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this folio.');
        }

        // Only certain roles can add charges
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST', 'ACCOUNTANT'])) {
            return back()->with('error', 'You do not have permission to add charges.');
        }

        // Check if folio is open
        if ($folio->status === 'CLOSED') {
            return back()->with('error', 'Cannot add charges to a closed folio.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'type' => 'required|in:ROOM,F&B,SPA,OTHER,DEPOSIT,REFUND',
        ]);

        try {
            DB::beginTransaction();

            $folioItem = FolioItem::create([
                'folio_id' => $folio->id,
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'service_charge' => $validated['service_charge'] ?? 0,
                'type' => $validated['type'],
                'created_by' => $user->id,
            ]);

            // Update folio balance
            $this->recalculateFolioBalance($folio);

            DB::commit();

            return back()->with('success', 'Charge added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add charge: ' . $e->getMessage());
        }
    }

    /**
     * Record a payment
     */
    public function addPayment(Request $request, Folio $folio)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this folio.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST', 'ACCOUNTANT'])) {
            return back()->with('error', 'You do not have permission to record payments.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:CASH,BANK,MOBILE,CARD',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_details' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'folio_id' => $folio->id,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'status' => 'COMPLETED',
                'transaction_id' => $validated['transaction_id'] ?? null,
                'payment_date' => now(),
                'payment_details' => $validated['payment_details'] ?? null,
                'currency' => $folio->currency,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            // Update folio balance
            $this->recalculateFolioBalance($folio);

            // If balance is zero or negative, suggest closing the folio
            if ($folio->fresh()->balance <= 0 && $folio->status === 'OPEN') {
                $message = 'Payment recorded successfully. Folio is fully paid and can be closed.';
            } else {
                $message = 'Payment recorded successfully.';
            }

            DB::commit();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice from folio
     */
    public function generateInvoice(Request $request, Folio $folio)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this folio.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST', 'ACCOUNTANT'])) {
            return back()->with('error', 'You do not have permission to generate invoices.');
        }

        $validated = $request->validate([
            'type' => 'required|in:PROFORMA,ACTUAL',
        ]);

        try {
            DB::beginTransaction();

            // Check if invoice of this type already exists
            $existingInvoice = Invoice::where('folio_id', $folio->id)
                ->where('type', $validated['type'])
                ->first();

            if ($existingInvoice) {
                return back()->with('error', 'An invoice of this type already exists for this folio.');
            }

            // Calculate totals
            $amount = $folio->folioItems()->sum('amount');
            $taxAmount = $folio->folioItems()->sum('tax_amount');
            $serviceCharge = $folio->folioItems()->sum('service_charge');
            $totalAmount = $amount + $taxAmount + $serviceCharge;

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($folio->reservation->property_id, $validated['type']);

            $invoice = Invoice::create([
                'folio_id' => $folio->id,
                'type' => $validated['type'],
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
                'currency' => $folio->currency,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Close a folio
     */
    public function close(Folio $folio)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this folio.');
        }

        // Only DIRECTOR, MANAGER, ACCOUNTANT can close folios
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return back()->with('error', 'You do not have permission to close folios.');
        }

        if ($folio->status === 'CLOSED') {
            return back()->with('error', 'Folio is already closed.');
        }

        // Check if folio has outstanding balance
        $this->recalculateFolioBalance($folio);
        $folio->refresh();

        if ($folio->balance > 0) {
            return back()->with('error', 'Cannot close folio with outstanding balance.');
        }

        try {
            $folio->update([
                'status' => 'CLOSED',
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Folio closed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to close folio: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate folio balance
     */
    private function recalculateFolioBalance(Folio $folio)
    {
        $totalCharges = $folio->folioItems()
            ->sum(DB::raw('amount + tax_amount + service_charge'));
        
        $totalPayments = $folio->payments()
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $balance = $totalCharges - $totalPayments;

        $folio->update(['balance' => $balance]);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber($propertyId, $type)
    {
        $prefix = $type === 'PROFORMA' ? 'PRO' : 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get last invoice number for this property, type, and month
        $lastInvoice = Invoice::whereHas('folio.reservation', function($q) use ($propertyId) {
            $q->where('property_id', $propertyId);
        })
        ->where('type', $type)
        ->where('invoice_number', 'LIKE', "{$prefix}-{$year}{$month}%")
        ->orderBy('invoice_number', 'desc')
        ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $newNumber);
    }
}

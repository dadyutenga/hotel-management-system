<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access invoices.');
        }

        $query = Invoice::with(['folio.reservation.guest', 'folio.reservation.property', 'creator']);

        // Filter by tenant's properties
        if ($user->role->name === 'DIRECTOR') {
            $query->whereHas('folio.reservation.property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->whereHas('folio.reservation', function($q) use ($user) {
                $q->where('property_id', $user->property_id);
            });
        } else {
            $query->whereHas('folio.reservation.property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_number', 'ILIKE', "%{$request->search}%");
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('generated_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('generated_at', '<=', $request->end_date);
        }

        $invoices = $query->latest('generated_at')->paginate(20);

        return view('Users.tenant.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($invoice->folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to view invoices.');
        }

        $invoice->load([
            'folio.reservation.guest',
            'folio.reservation.property',
            'folio.folioItems',
            'payments',
            'creator'
        ]);

        return view('Users.tenant.invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($invoice->folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT', 'RECEPTIONIST'])) {
            return back()->with('error', 'You do not have permission to download invoices.');
        }

        // TODO: Implement PDF generation using a package like dompdf or snappy
        // For now, return a message
        return back()->with('info', 'PDF generation feature will be implemented soon.');
    }

    /**
     * Email invoice to guest
     */
    public function email(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($invoice->folio->reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT', 'RECEPTIONIST'])) {
            return back()->with('error', 'You do not have permission to email invoices.');
        }

        // TODO: Implement email functionality
        return back()->with('info', 'Email functionality will be implemented soon.');
    }
}

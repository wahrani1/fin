<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CertifiedResearcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertifiedResearcherController extends Controller
{
    public function index(Request $request)
    {
        $query = CertifiedResearcher::with('user');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $certifications = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('dashboard.certified_researchers.index', compact('certifications'));
    }

    public function show(CertifiedResearcher $certifiedResearcher)
    {
        // Load the user relationship if not already loaded
        $certifiedResearcher->load('user');
        return view('dashboard.certified_researchers.show', compact('certifiedResearcher'));
    }

    public function approve(CertifiedResearcher $certifiedResearcher)
    {
        // Load the user relationship if not already loaded
        $certifiedResearcher->load('user');

        $user = $certifiedResearcher->user;

        // Check if user exists
        if (!$user) {
            return redirect()->route('certified_researchers.index')
                ->with('error', 'Associated user not found.');
        }

        // Check if already approved
        if ($certifiedResearcher->status === 'accepted') {
            return redirect()->route('certified_researchers.index')
                ->with('error', 'This application has already been approved.');
        }

        // Check user type
        if ($user->type !== 'normal') {
            return redirect()->route('certified_researchers.index')
                ->with('error', 'User is not a normal user. Current type: ' . $user->type);
        }

        // Update user type and application status
        $user->update(['type' => 'researcher']);
        $certifiedResearcher->update([
            'status' => 'accepted',
            'rejection_reason' => null // Clear any previous rejection reason
        ]);

        return redirect()->route('certified_researchers.index')
            ->with('success', "User '{$user->name}' has been successfully upgraded to researcher.");
    }

    public function reject(Request $request, CertifiedResearcher $certifiedResearcher)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        // Check if already rejected
        if ($certifiedResearcher->status === 'rejected') {
            return redirect()->route('certified_researchers.index')
                ->with('error', 'This application has already been rejected.');
        }

        $certifiedResearcher->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->route('certified_researchers.index')
            ->with('success', 'Application has been rejected successfully.');
    }

    public function destroy(CertifiedResearcher $certifiedResearcher)
    {
        // Delete the associated file if it exists
        if ($certifiedResearcher->file) {
            Storage::disk('public')->delete($certifiedResearcher->file);
        }

        $userName = $certifiedResearcher->user->name ?? 'Unknown User';
        $certifiedResearcher->delete();

        return redirect()->route('certified_researchers.index')
            ->with('success', "Application for '{$userName}' has been deleted successfully.");
    }
}

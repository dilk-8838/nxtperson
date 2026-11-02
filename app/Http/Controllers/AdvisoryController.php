<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\Crop;
use App\Services\Advisory\CropAdvisoryEngineInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdvisoryController extends Controller
{
    protected CropAdvisoryEngineInterface $advisoryEngine;

    public function __construct(CropAdvisoryEngineInterface $advisoryEngine)
    {
        $this->advisoryEngine = $advisoryEngine;
    }

    /**
     * Display the advisory hub with diagnostic submission form and recent cases.
     */
    public function index()
    {
        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();
        $recentAdvisories = Advisory::orderBy('created_at', 'desc')->take(6)->get();

        return view('pages.advisory-hub', compact('crops', 'recentAdvisories'));
    }

    /**
     * Store a newly created crop advisory request and run AI diagnosis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'crop_slug' => 'required|string|exists:crops,slug',
            'issue_description' => 'required|string|min:5|max:1000',
            'symptom_key' => 'nullable|string|max:50',
            'farmer_name' => 'nullable|string|max:100',
            'farmer_phone' => 'nullable|string|max:20',
            'farmer_district' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $user = Auth::user();
        $cropSlug = $request->input('crop_slug');
        $symptomKey = $request->input('symptom_key');

        // Fallback default symptom detection from description if not selected
        if (empty($symptomKey)) {
            $desc = strtolower($request->input('issue_description'));
            if (str_contains($desc, 'bollworm') || str_contains($desc, 'hole') || str_contains($desc, 'worm')) {
                $symptomKey = 'bollworm';
            } elseif (str_contains($desc, 'curl') || str_contains($desc, 'yellow') || str_contains($desc, 'wrinkle')) {
                $symptomKey = 'curl';
            } elseif (str_contains($desc, 'blight') || str_contains($desc, 'spot') || str_contains($desc, 'rot')) {
                $symptomKey = 'blight';
            } elseif (str_contains($desc, 'rust') || str_contains($desc, 'pustule')) {
                $symptomKey = 'rust';
            } else {
                $symptomKey = 'stress';
            }
        }

        // Handle leaf image upload with strict type check and randomized filename
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $safeExt = in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png', 'webp']) ? strtolower($file->extension()) : 'jpg';
            $filename = 'adv_'.time().'_'.Str::random(16).'.'.$safeExt;
            $targetDir = public_path('uploads'.DIRECTORY_SEPARATOR.'advisories');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            try {
                $file->move($targetDir, $filename);
            } catch (\Throwable $e) {
                @copy($file->getPathname(), $targetDir.DIRECTORY_SEPARATOR.$filename);
            }
            $imagePath = 'uploads/advisories/'.$filename;
        }

        // Run diagnosis through modular engine
        $diagnosis = $this->advisoryEngine->diagnose($cropSlug, $symptomKey, $imagePath);

        // Generate clean ticket number
        $ticketNumber = 'NX-'.strtoupper(substr($cropSlug, 0, 3)).'-'.date('Ymd').'-'.rand(1000, 9999);

        // Farmer identity (from authenticated session or input)
        $farmerName = $user ? $user->name : ($request->input('farmer_name') ?: 'Suresh Patel (Demo Farmer)');
        $farmerPhone = $user ? $user->phone : ($request->input('farmer_phone') ?: '9876543210');
        $farmerDistrict = $user ? ($user->district ?: 'Rajkot') : ($request->input('farmer_district') ?: 'Rajkot');

        $advisory = Advisory::create([
            'ticket_number' => $ticketNumber,
            'crop_slug' => $cropSlug,
            'farmer_name' => $farmerName,
            'farmer_phone' => $farmerPhone,
            'farmer_district' => $farmerDistrict,
            'issue_description' => $request->input('issue_description'),
            'image_path' => $imagePath,
            'symptom_key' => $symptomKey,
            'ai_diagnosis_title' => $diagnosis['title'],
            'ai_diagnosis_description' => $diagnosis['description'],
            'ai_recommended_solution' => $diagnosis['solution'],
            'ai_confidence' => $diagnosis['confidence'] ?? '94% Confidence',
            'status' => 'ai_analyzed',
            'assigned_agronomist' => null,
            'agronomist_notes' => null,
            'is_demonstration' => true,
        ]);

        return redirect()->route('advisory.show', $advisory->ticket_number)
            ->with('success', "Advisory ticket #{$advisory->ticket_number} created successfully. Simulated AI diagnosis generated.");
    }

    /**
     * Display a specific advisory ticket and diagnostic report.
     */
    public function show(string $ticketNumber)
    {
        $advisory = Advisory::where('ticket_number', $ticketNumber)->firstOrFail();
        $crop = Crop::where('slug', $advisory->crop_slug)->first();

        return view('pages.advisory-detail', compact('advisory', 'crop'));
    }

    /**
     * Escalate an advisory ticket to an agronomist.
     */
    public function escalate(string $ticketNumber)
    {
        $advisory = Advisory::where('ticket_number', $ticketNumber)->firstOrFail();

        $agronomists = [
            'cotton' => 'Demonstration Agronomic Consultant (Cotton & Pest Management)',
            'potato' => 'Demonstration Plant Pathologist (Tuber Diseases Desk)',
            'banana' => 'Demonstration Agronomist (Horticulture Support)',
            'chilli' => 'Demonstration Crop Protection Specialist (Spices Hub)',
            'rice' => 'Demonstration Paddy Specialist (Cereal Health)',
            'grain' => 'Demonstration Wheat & Grain Agronomist',
            'tomato' => 'Demonstration Vegetable Agronomic Consultant',
            'cauliflower' => 'Demonstration Cruciferous Crop Specialist',
        ];

        $assigned = $agronomists[$advisory->crop_slug] ?? 'Demonstration Senior Agronomist';

        $advisory->update([
            'status' => 'escalated',
            'assigned_agronomist' => $assigned,
            'agronomist_notes' => 'Ticket triaged for agronomic review in demonstration queue. Recommended initial biological foliar application noted.',
        ]);

        return redirect()->route('advisory.show', $advisory->ticket_number)
            ->with('success', "Ticket #{$advisory->ticket_number} has been escalated to {$assigned}. Demonstration status updated.");
    }

    /**
     * Display advisory history for farmer.
     */
    public function history()
    {
        $user = Auth::user();

        // If authenticated farmer, query by name/phone or all recent demo tickets
        if ($user) {
            $advisories = Advisory::where(function ($q) use ($user) {
                $q->where('farmer_phone', $user->phone)
                    ->orWhere('farmer_name', $user->name);
            })->orWhere('is_demonstration', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $advisories = Advisory::orderBy('created_at', 'desc')->paginate(12);
        }

        return view('pages.advisory-history', compact('advisories'));
    }
}

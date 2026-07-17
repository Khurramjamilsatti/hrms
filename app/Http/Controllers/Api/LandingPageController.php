<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingFeature;
use App\Models\LandingSetting;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Public payload for the marketing landing page.
     */
    public function public()
    {
        $settings = LandingSetting::current();

        if (!$settings->is_published) {
            return response()->json([
                'settings' => [
                    'brand_name' => $settings->brand_name,
                    'hero_title' => 'Coming soon',
                    'hero_subtitle' => 'Our website is being updated. Please check back shortly.',
                    'hero_cta_text' => 'Sign In',
                    'hero_cta_link' => '/login',
                    'is_published' => false,
                ],
                'features' => [],
                'stats' => [],
                'testimonials' => [],
            ]);
        }

        return response()->json([
            'settings' => $settings,
            'features' => LandingFeature::where('is_active', true)->orderBy('sort_order')->get(),
            'stats' => LandingStat::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show()
    {
        return response()->json([
            'settings' => LandingSetting::current(),
            'features' => LandingFeature::orderBy('sort_order')->get(),
            'stats' => LandingStat::orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::orderBy('sort_order')->get(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:120',
            'brand_tagline' => 'nullable|string|max:180',
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'hero_cta_text' => 'nullable|string|max:80',
            'hero_cta_link' => 'nullable|string|max:255',
            'hero_secondary_cta_text' => 'nullable|string|max:80',
            'hero_secondary_cta_link' => 'nullable|string|max:255',
            'about_title' => 'nullable|string|max:255',
            'about_body' => 'nullable|string|max:5000',
            'features_title' => 'nullable|string|max:255',
            'features_subtitle' => 'nullable|string|max:500',
            'stats_title' => 'nullable|string|max:255',
            'testimonials_title' => 'nullable|string|max:255',
            'cta_title' => 'nullable|string|max:255',
            'cta_body' => 'nullable|string|max:1000',
            'cta_button_text' => 'nullable|string|max:80',
            'cta_button_link' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ]);

        $settings = LandingSetting::current();
        $settings->update($validated);

        return response()->json([
            'message' => 'Landing page settings updated.',
            'settings' => $settings->fresh(),
        ]);
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:80',
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingFeature::max('sort_order') + 1);
        $feature = LandingFeature::create($validated);

        return response()->json(['message' => 'Feature created.', 'feature' => $feature], 201);
    }

    public function updateFeature(Request $request, LandingFeature $feature)
    {
        $validated = $request->validate([
            'icon' => 'nullable|string|max:80',
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $feature->update($validated);

        return response()->json(['message' => 'Feature updated.', 'feature' => $feature]);
    }

    public function destroyFeature(LandingFeature $feature)
    {
        $feature->delete();
        return response()->json(['message' => 'Feature deleted.']);
    }

    public function storeStat(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:120',
            'value' => 'required|string|max:80',
            'icon' => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingStat::max('sort_order') + 1);
        $stat = LandingStat::create($validated);

        return response()->json(['message' => 'Stat created.', 'stat' => $stat], 201);
    }

    public function updateStat(Request $request, LandingStat $stat)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:120',
            'value' => 'required|string|max:80',
            'icon' => 'nullable|string|max:80',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $stat->update($validated);

        return response()->json(['message' => 'Stat updated.', 'stat' => $stat]);
    }

    public function destroyStat(LandingStat $stat)
    {
        $stat->delete();
        return response()->json(['message' => 'Stat deleted.']);
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'role' => 'nullable|string|max:120',
            'company' => 'nullable|string|max:120',
            'quote' => 'required|string|max:2000',
            'avatar_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingTestimonial::max('sort_order') + 1);
        $testimonial = LandingTestimonial::create($validated);

        return response()->json(['message' => 'Testimonial created.', 'testimonial' => $testimonial], 201);
    }

    public function updateTestimonial(Request $request, LandingTestimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'role' => 'nullable|string|max:120',
            'company' => 'nullable|string|max:120',
            'quote' => 'required|string|max:2000',
            'avatar_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $testimonial->update($validated);

        return response()->json(['message' => 'Testimonial updated.', 'testimonial' => $testimonial]);
    }

    public function destroyTestimonial(LandingTestimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(['message' => 'Testimonial deleted.']);
    }
}

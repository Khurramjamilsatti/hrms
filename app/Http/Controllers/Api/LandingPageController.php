<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingPage;
use App\Models\LandingPlan;
use App\Models\LandingSetting;
use App\Models\LandingStat;
use App\Models\LandingStep;
use App\Models\LandingTestimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                'plans' => [],
                'faqs' => [],
                'steps' => [],
                'pages' => [],
            ]);
        }

        return response()->json([
            'settings' => $settings,
            'features' => LandingFeature::where('is_active', true)->orderBy('sort_order')->get(),
            'stats' => LandingStat::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'plans' => LandingPlan::where('is_active', true)->orderBy('sort_order')->get(),
            'faqs' => LandingFaq::where('is_active', true)->orderBy('sort_order')->get(),
            'steps' => LandingStep::where('is_active', true)->orderBy('sort_order')->get(),
            'pages' => LandingPage::where('show_in_footer', true)
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(['id', 'slug', 'title', 'nav_label', 'excerpt', 'sort_order']),
        ]);
    }

    /**
     * Return a single published landing page by slug.
     */
    public function publicPage(string $slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_published', true)->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return response()->json(['page' => $page]);
    }

    /**
     * List published pages for footer/nav rendering.
     */
    public function publicPages()
    {
        $pages = LandingPage::where('is_published', true)
            ->orderBy('sort_order')
            ->get(['id', 'slug', 'title', 'nav_label', 'excerpt', 'sort_order']);

        return response()->json(['pages' => $pages]);
    }

    public function show()
    {
        return response()->json([
            'settings' => LandingSetting::current(),
            'features' => LandingFeature::orderBy('sort_order')->get(),
            'stats' => LandingStat::orderBy('sort_order')->get(),
            'testimonials' => LandingTestimonial::orderBy('sort_order')->get(),
            'plans' => LandingPlan::orderBy('sort_order')->get(),
            'faqs' => LandingFaq::orderBy('sort_order')->get(),
            'steps' => LandingStep::orderBy('sort_order')->get(),
            'pages' => LandingPage::orderBy('sort_order')->get(),
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
            'pricing_title' => 'nullable|string|max:255',
            'pricing_subtitle' => 'nullable|string|max:500',
            'faq_title' => 'nullable|string|max:255',
            'faq_subtitle' => 'nullable|string|max:500',
            'how_it_works_title' => 'nullable|string|max:255',
            'how_it_works_subtitle' => 'nullable|string|max:500',
            'security_title' => 'nullable|string|max:255',
            'security_body' => 'nullable|string|max:3000',
            'contact_title' => 'nullable|string|max:255',
            'contact_body' => 'nullable|string|max:2000',
            'cta_title' => 'nullable|string|max:255',
            'cta_body' => 'nullable|string|max:1000',
            'cta_button_text' => 'nullable|string|max:80',
            'cta_button_link' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
            'social_linkedin' => 'nullable|string|max:255',
            'social_twitter' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|string|max:255',
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

    /**
     * Normalize incoming "features" data into an array of non-empty strings.
     * Accepts either an array of strings or a newline-separated string.
     */
    protected function normalizeFeatureLines($features): array
    {
        if (is_array($features)) {
            $lines = $features;
        } else {
            $lines = preg_split('/\r\n|\r|\n/', (string) $features);
        }

        return array_values(array_filter(array_map(function ($line) {
            return is_string($line) ? trim($line) : trim((string) $line);
        }, $lines), fn ($line) => $line !== ''));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|unique:landing_plans,slug',
            'price' => 'nullable|string|max:80',
            'price_period' => 'nullable|string|max:80',
            'badge' => 'nullable|string|max:80',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable',
            'cta_text' => 'nullable|string|max:80',
            'cta_link' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['features'] = $this->normalizeFeatureLines($request->input('features', []));
        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingPlan::max('sort_order') + 1);

        $plan = LandingPlan::create($validated);

        return response()->json(['message' => 'Plan created.', 'plan' => $plan], 201);
    }

    public function updatePlan(Request $request, LandingPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|unique:landing_plans,slug,' . $plan->id,
            'price' => 'nullable|string|max:80',
            'price_period' => 'nullable|string|max:80',
            'badge' => 'nullable|string|max:80',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable',
            'cta_text' => 'nullable|string|max:80',
            'cta_link' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['features'] = $this->normalizeFeatureLines($request->input('features', []));

        $plan->update($validated);

        return response()->json(['message' => 'Plan updated.', 'plan' => $plan]);
    }

    public function destroyPlan(LandingPlan $plan)
    {
        $plan->delete();
        return response()->json(['message' => 'Plan deleted.']);
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:3000',
            'category' => 'nullable|string|max:80',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingFaq::max('sort_order') + 1);
        $faq = LandingFaq::create($validated);

        return response()->json(['message' => 'FAQ created.', 'faq' => $faq], 201);
    }

    public function updateFaq(Request $request, LandingFaq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:3000',
            'category' => 'nullable|string|max:80',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $faq->update($validated);

        return response()->json(['message' => 'FAQ updated.', 'faq' => $faq]);
    }

    public function destroyFaq(LandingFaq $faq)
    {
        $faq->delete();
        return response()->json(['message' => 'FAQ deleted.']);
    }

    public function storeStep(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:80',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingStep::max('sort_order') + 1);
        $step = LandingStep::create($validated);

        return response()->json(['message' => 'Step created.', 'step' => $step], 201);
    }

    public function updateStep(Request $request, LandingStep $step)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:80',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $step->update($validated);

        return response()->json(['message' => 'Step updated.', 'step' => $step]);
    }

    public function destroyStep(LandingStep $step)
    {
        $step->delete();
        return response()->json(['message' => 'Step deleted.']);
    }

    public function storePage(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:120|unique:landing_pages,slug',
            'title' => 'required|string|max:255',
            'nav_label' => 'nullable|string|max:80',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'show_in_footer' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['sort_order'] = $validated['sort_order'] ?? ((int) LandingPage::max('sort_order') + 1);

        $page = LandingPage::create($validated);

        return response()->json(['message' => 'Page created.', 'page' => $page], 201);
    }

    public function updatePage(Request $request, LandingPage $page)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:120|unique:landing_pages,slug,' . $page->id,
            'title' => 'required|string|max:255',
            'nav_label' => 'nullable|string|max:80',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'show_in_footer' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);

        $page->update($validated);

        return response()->json(['message' => 'Page updated.', 'page' => $page]);
    }

    public function destroyPage(LandingPage $page)
    {
        $page->delete();
        return response()->json(['message' => 'Page deleted.']);
    }
}

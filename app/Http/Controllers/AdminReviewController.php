<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('moderate', Review::class);

        $query = Review::query()
            ->with(['user', 'hotel']);

        if (Schema::hasColumn('reports', 'review_id')) {
            $query->withCount('reports');
        }

        $reviews = $query
            ->when($request->query('filter') === 'banned', fn ($q) => $q->where('is_banned', true))
            ->when($request->query('filter') === 'visible', fn ($q) => $q->where('is_banned', false))
            ->when(
                $request->query('filter') === 'reported' && Schema::hasColumn('reports', 'review_id'),
                fn ($q) => $q->whereHas('reports')
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function ban(Review $review): RedirectResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['is_banned' => true]);

        return redirect()
            ->route('manage.admin.reviews.index', array_filter(['filter' => request('filter')]))
            ->with('success', 'Opinia została ukryta (ban).');
    }

    public function unban(Review $review): RedirectResponse
    {
        $this->authorize('moderate', Review::class);

        $review->update(['is_banned' => false]);

        return redirect()
            ->route('manage.admin.reviews.index', array_filter(['filter' => request('filter')]))
            ->with('success', 'Opinia została przywrócona.');
    }
}

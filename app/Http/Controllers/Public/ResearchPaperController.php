<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResearchPaperRequest;
use App\Models\ResearchPaper;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ResearchPaperController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchPaper::query()->where('review_status', 'approved')->where('is_published', true);

        if ($search = trim($request->string('q')->toString())) {
            $term = like_term($search);
            $query->where(function ($inner) use ($term) {
                $inner->where('title', 'like', $term)
                    ->orWhere('authors', 'like', $term)
                    ->orWhere('keywords', 'like', $term);
            });
        }

        if ($category = $request->string('category')->toString()) {
            if (array_key_exists($category, ResearchPaper::categories())) {
                $query->where('category', $category);
            }
        }

        if ($year = $request->integer('year')) {
            $query->where('publication_year', $year);
        }

        if ($status = $request->string('publication_status')->toString()) {
            if (array_key_exists($status, ResearchPaper::publicationStatuses())) {
                $query->where('publication_status', $status);
            }
        }

        return view('public.research.index', [
            'papers' => $query->orderByDesc('publication_year')->orderByDesc('published_at')->orderByDesc('id')->paginate(12)->withQueryString(),
            'categories' => ResearchPaper::categories(),
            'publicationStatuses' => ResearchPaper::publicationStatuses(),
            'years' => ResearchPaper::query()
                ->where('review_status', 'approved')
                ->where('is_published', true)
                ->whereNotNull('publication_year')
                ->distinct()
                ->orderByDesc('publication_year')
                ->pluck('publication_year'),
        ]);
    }

    public function show(Request $request, string $paper)
    {
        $record = $this->readable($request, $paper);

        return view('public.research.show', [
            'paper' => $record,
            'canReview' => $request->user()?->hasAdminAccess() === true,
        ]);
    }

    public function pdf(Request $request, string $paper)
    {
        $record = $this->readable($request, $paper);

        if (! $record->pdf_path || ! Storage::disk('local')->exists($record->pdf_path)) {
            abort(404);
        }

        $filename = str($record->title)->slug()->toString().'.pdf';
        $headers = ['Content-Type' => 'application/pdf'];

        if ($request->boolean('download')) {
            return Storage::disk('local')->download($record->pdf_path, $filename, $headers);
        }

        return Storage::disk('local')->response($record->pdf_path, $filename, $headers);
    }

    public function create()
    {
        $this->authorize('submit', ResearchPaper::class);

        return view('public.research.submit', [
            'categories' => ResearchPaper::categories(),
            'publicationStatuses' => ResearchPaper::publicationStatuses(),
        ]);
    }

    public function store(StoreResearchPaperRequest $request)
    {
        $this->authorize('submit', ResearchPaper::class);

        $data = $request->safe()->except(['pdf']);
        foreach (['keywords', 'doi', 'external_url', 'citation', 'supplementary'] as $field) {
            $data[$field] = ($data[$field] ?? null) ?: null;
        }
        $data['slug'] = unique_slug(ResearchPaper::class, $data['title']);
        $data['pdf_path'] = $this->storePdf($request);
        $data['review_status'] = 'pending';
        $data['is_published'] = false;
        $data['submitted_by'] = $request->user()->id;

        $paper = ResearchPaper::query()->create($data);
        ActivityLogger::log('create', 'research_papers', $paper->id, 'Submitted research paper');

        return redirect()->route('member.dashboard')->with('status', 'Research paper submitted. It stays hidden until an administrator approves and publishes it.');
    }

    private function readable(Request $request, string $slug): ResearchPaper
    {
        $paper = ResearchPaper::query()->where('slug', $slug)->first();

        if (! $paper || ! $paper->canBeReadBy($request->user())) {
            abort(404);
        }

        return $paper;
    }

    private function storePdf(Request $request): string
    {
        try {
            return SecureUpload::pdf($request->file('pdf'), 'research-papers');
        } catch (ValidationException $exception) {
            $message = $exception->validator->errors()->first('file')
                ?: 'The PDF could not be uploaded.';

            throw ValidationException::withMessages(['pdf' => $message]);
        }
    }
}

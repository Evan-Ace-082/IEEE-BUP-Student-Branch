<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResearchPaperRequest;
use App\Models\ResearchPaper;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResearchPaperController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('review', ResearchPaper::class);

        $query = ResearchPaper::query()->with(['submitter', 'reviewer'])->latest();

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        if ($status = $request->string('review_status')->toString()) {
            if (array_key_exists($status, ResearchPaper::reviewStatuses())) {
                $query->where('review_status', $status);
            }
        }

        return view('admin.research.index', [
            'papers' => $query->paginate(12)->withQueryString(),
            'statuses' => ResearchPaper::reviewStatuses(),
        ]);
    }

    public function show(ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);
        $researchPaper->load(['submitter', 'reviewer']);

        return view('admin.research.show', [
            'paper' => $researchPaper,
            'categories' => ResearchPaper::categories(),
            'publicationStatuses' => ResearchPaper::publicationStatuses(),
        ]);
    }

    public function update(ResearchPaperRequest $request, ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);

        $data = $request->safe()->except(['pdf']);
        foreach (['keywords', 'doi', 'external_url', 'citation', 'supplementary'] as $field) {
            $data[$field] = ($data[$field] ?? null) ?: null;
        }
        $data['slug'] = unique_slug(ResearchPaper::class, $data['title'], $researchPaper->id);

        if ($request->file('pdf')) {
            SecureUpload::delete($researchPaper->pdf_path, 'local');
            $data['pdf_path'] = $this->storePdf($request);
        }

        $researchPaper->update($data);
        ActivityLogger::log('update', 'research_papers', $researchPaper->id, 'Updated research paper metadata');

        return redirect()->route('admin.research-papers.show', $researchPaper)->with('status', 'Research paper details saved.');
    }

    public function approve(Request $request, ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);
        $note = $request->validate(['review_note' => ['nullable', 'string', 'max:2000']])['review_note'] ?? null;

        $researchPaper->update([
            'review_status' => 'approved',
            'review_note' => $note ?: null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        ActivityLogger::log('approve', 'research_papers', $researchPaper->id, 'Approved research paper');

        return back()->with('status', 'Paper approved. Publish it when it should appear on the public page.');
    }

    public function reject(Request $request, ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);
        $note = $request->validate(['review_note' => ['nullable', 'string', 'max:2000']])['review_note'] ?? null;

        $researchPaper->update([
            'review_status' => 'rejected',
            'is_published' => false,
            'review_note' => $note ?: null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        ActivityLogger::log('reject', 'research_papers', $researchPaper->id, 'Rejected research paper');

        return back()->with('status', 'Paper rejected. It is not publicly accessible.');
    }

    public function publish(ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);

        if ($researchPaper->review_status !== 'approved') {
            return back()->withErrors(['review_status' => 'Approve the paper before publishing it.']);
        }

        $researchPaper->update([
            'is_published' => true,
            'published_at' => $researchPaper->published_at ?: now(),
        ]);
        ActivityLogger::log('publish', 'research_papers', $researchPaper->id, 'Published research paper');

        return back()->with('status', 'Paper published.');
    }

    public function unpublish(ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);

        $researchPaper->update(['is_published' => false]);
        ActivityLogger::log('unpublish', 'research_papers', $researchPaper->id, 'Unpublished research paper');

        return back()->with('status', 'Paper unpublished.');
    }

    public function destroy(ResearchPaper $researchPaper)
    {
        $this->authorize('review', $researchPaper);

        SecureUpload::delete($researchPaper->pdf_path, 'local');
        $researchPaper->delete();
        ActivityLogger::log('delete', 'research_papers', $researchPaper->id, 'Deleted research paper');

        return redirect()->route('admin.research-papers.index')->with('status', 'Research paper removed.');
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

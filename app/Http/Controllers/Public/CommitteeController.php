<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExecutiveCommittee;

class CommitteeController extends Controller
{
    public function index()
    {
        $committees = ExecutiveCommittee::query()
            ->with(['members.position'])
            ->orderByDesc('is_current')
            ->orderByDesc('year')
            ->get()
            ->each(function (ExecutiveCommittee $committee) {
                $committee->setRelation(
                    'members',
                    $committee->members
                        ->sortBy(fn ($member) => sprintf('%05d-%05d', $member->position?->sort_order ?? 999, $member->sort_order))
                        ->values()
                );
            });

        return view('public.committee', compact('committees'));
    }
}

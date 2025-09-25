<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::query()->findOrFail($request->input('plan_id'));

        $user = Auth::user();
        $user->plan()->associate($plan);
        $user->save();

        return redirect()->route('chat')->with('status', 'Plan updated to '.$plan->name.'.');
    }
}



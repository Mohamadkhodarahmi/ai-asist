<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index()
    {

        $plans = Plan::all();

        return view('pricing', compact('plans'));
    }

    public function select(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($request->input('plan_id'));

        $user = Auth::user();
        $user->plan()->associate($plan);
        $user->save();

        return redirect()->route('chat')->with('status', 'Plan updated to '.$plan->name.'.');
    }
}

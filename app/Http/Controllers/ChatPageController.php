<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatPageController extends Controller
{
    /**
     * Display the chat page.
     *
     * The view itself contains the logic to show either the creation
     * form or the chat interface based on whether the user has a "business".
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Removed the trailing dot from the view name
        return view('livewire.chat-interface');
    }
}


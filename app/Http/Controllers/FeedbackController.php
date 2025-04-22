<?php

namespace App\Http\Controllers;
use App\Models\Feedback;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function submitFeedback(Request $request)
    {
        // Validate the incoming feedback data
        $request->validate([
            'message' => 'required|string|max:500',
        ]);
    
        // Store the feedback in the database with `read_at` set to null (unread by default)
        Feedback::create([
            'message' => $request->input('message'),
            'read_at' => null,  // Feedback is unread by default
        ]);
    
        // Optionally, you can return a response indicating success
        return response()->json([
            'message' => 'Thank you for your feedback!'
        ]);
    }
}
    
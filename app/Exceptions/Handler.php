<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }



protected function unauthenticated($request, AuthenticationException $exception)
{
    if ($request->expectsJson()) {
        // Return 200 instead of 401 if you want to avoid frontend console error
        return response()->json([
            'user' => null,
            'message' => 'Not authenticated',
        ], 200); // 👈 Important: return 200, not 401
    }

    // For non-JSON requests (e.g., web), you can redirect or just return 403
    return response()->json([
        'user' => null,
        'message' => 'Unauthorized',
    ], 403);
}


    
}

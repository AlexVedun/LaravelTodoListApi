<?php

namespace App\Exceptions;

use App\Services\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LogicException;
use Throwable;

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

    public function render($request, Throwable $e)
    {
        $responseService = app(ResponseService::class);

        if ($e instanceof AuthorizationException && $request->wantsJson()) {
            return response()->json(
                $responseService->getErrorResponse('User not allowed to make this action'),
                Response::HTTP_FORBIDDEN
            );
        }
        if ($e instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json(
                $responseService->getErrorResponse('Requested model was not found'),
                Response::HTTP_NOT_FOUND
            );
        }
        if ($e instanceof LogicException && $request->wantsJson()) {
            return response()->json(
                $responseService->getErrorResponse('Cannot make the action due to internal error'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return parent::render($request, $e);
    }
}

<?php

namespace App\Exceptions;

use App\Domain\Exceptions\CBRHostNotAvailable;
use App\Domain\Exceptions\InvalidDataException;
use App\Domain\Exceptions\UnexpectedDataProvided;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
        $this->renderable(function (CBRHostNotAvailable $e) {
            return new JsonResponse([
                'message' => 'Нет связи с сайтом ЦБ'
            ], 500);
        });

        $this->renderable(function (InvalidDataException $e) {
            return new JsonResponse([
                'message' => 'Непредвиденная ошибка, попробуйте позже'
            ], 500);
        });

        $this->reportable(function (UnexpectedDataProvided $e) {
            // Еще и зарепортить в сентри будет неплохо
            Log::error($e->getMessage(), [
                'response' => $e->getResponse()
            ]);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

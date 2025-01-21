<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Router;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Str;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {


        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        }


        if ($e instanceof Responsable) {
            return $e->toResponse($request);
        }


        if ($e instanceof ModelNotFoundException) {
            $modelo = Str::lower(class_basename($e->getModel()));
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado.", 404);
        }


        if ($e instanceof AuthenticationException)
        {
            return $this->unauthenticated($request, $e);
        }


        if ($e instanceof AuthorizationException)
        {
            return $this->errorResponse("No posee permisos para ejecutar esta acción", 403);
        }


        if ($e instanceof NotFoundHttpException)
        {
            return $this->errorResponse("No se encontró la dirección especificada", 404);
        }




        if ($e instanceof MethodNotAllowedHttpException)
        {
            return $this->errorResponse("El metodo especificado en la petición no es válido", 405);
        }


        if ($e instanceof HttpException)
        {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }


        if ($e instanceof QueryException)
        {
            if ($e->errorInfo[1] == 1451)
            {
                return $this->errorResponse("No se puede eliminar el recurso porque tiene dependencias relacionadas", 409);
            }
        }

        if ($e instanceof TokenMismatchException)
        {
            return redirect()->back()->withInput($request->input());
        }


        if ($e->getCode() >= 400)
        {
            if (config('app.debug'))
            {
                $this->renderExceptionResponse($request, $e);
            }
            return $this->errorResponse("Falla inesperada, intentelo más tarde", 500);
        }


        $e = $this->prepareException($this->mapException($e));


        if ($response = $this->renderViaCallbacks($request, $e)) {
            return $response;
        }


        return match (true) {
            $e instanceof HttpResponseException => $e->getResponse(),
            $e instanceof AuthenticationException => $this->unauthenticated($request, $e),
            $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
            default => $this->errorResponse("Excepción no manejada", 500),
        };
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->isFrontend($request)
                    ? redirect()->guest('login')
                    : $this->errorResponse('No autenticado', 401);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {

        $errors = $e->validator->errors()->getMessages();

        if($this->isFrontend($request)){
            return $request->ajax() ?
            response()->json($errors, 422) :
            redirect()
            ->back()
            ->withInput($request->input())
            ->withErrors($errors);
        }

        return $this->errorResponse($errors, 422);


    }

    protected function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}

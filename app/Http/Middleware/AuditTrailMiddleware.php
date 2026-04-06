<?php

namespace App\Http\Middleware;

use App\Jobs\LogAuditTrailJob;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditTrailMiddleware
{
    /**
     * HTTP methods that modify data and must always be audited.
     */
    private const MUTATING_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Route path fragments that indicate a medical-record read.
     */
    private const MEDICAL_RECORD_PATTERNS = ['medical-records', 'rekam-medis'];

    /**
     * Handle an incoming request.
     * - Records audit trail for POST/PUT/PATCH/DELETE requests.
     * - Records read access for GET requests to medical-record routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $method = strtoupper($request->method());

        if (in_array($method, self::MUTATING_METHODS, true)) {
            $this->dispatchMutatingAudit($request, $method);
        } elseif ($method === 'GET' && $this->isMedicalRecordRoute($request)) {
            $this->dispatchReadAudit($request);
        }

        return $response;
    }

    private function dispatchMutatingAudit(Request $request, string $method): void
    {
        $action = $this->deriveAction($method, $request);
        [$modelType, $modelId] = $this->resolveModel($request);

        $oldValues = in_array($method, ['PUT', 'PATCH'], true)
            ? $request->except(['_token', '_method', 'password', 'password_confirmation'])
            : null;

        $newValues = in_array($method, ['POST', 'PUT', 'PATCH'], true)
            ? $request->except(['_token', '_method', 'password', 'password_confirmation'])
            : null;

        LogAuditTrailJob::dispatch(
            Auth::id(),
            $action,
            $modelType,
            $modelId,
            $oldValues,
            $newValues,
            $request->ip(),
        );
    }

    private function dispatchReadAudit(Request $request): void
    {
        [, $modelId] = $this->resolveModel($request);

        LogAuditTrailJob::dispatch(
            Auth::id(),
            'read_medical_record',
            null,
            $modelId,
            null,
            null,
            $request->ip(),
        );
    }

    /**
     * Derive a human-readable action name from the HTTP method and route.
     */
    private function deriveAction(string $method, Request $request): string
    {
        $routeName = $request->route()?->getName();

        if ($routeName !== null) {
            // e.g. "patients.store" → "create", "patients.destroy" → "delete"
            $parts = explode('.', $routeName);
            $suffix = end($parts);
            $map = [
                'store'   => 'create',
                'update'  => 'update',
                'destroy' => 'delete',
            ];
            if (isset($map[$suffix])) {
                return $map[$suffix];
            }
        }

        return match ($method) {
            'POST'   => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default  => strtolower($method),
        };
    }

    /**
     * Attempt to resolve model type and ID from route parameters.
     *
     * @return array{?string, ?int}
     */
    private function resolveModel(Request $request): array
    {
        $route = $request->route();
        if ($route === null) {
            return [null, null];
        }

        // Try to find a route parameter that looks like a model binding or numeric ID
        $parameters = $route->parameters();
        $modelId = null;
        $modelType = null;

        foreach ($parameters as $key => $value) {
            if (is_object($value)) {
                // Eloquent model bound via route model binding
                $modelType = get_class($value);
                $modelId = $value->getKey();
                break;
            }

            if (is_numeric($value)) {
                $modelId = (int) $value;
                // Derive a rough model type from the parameter name (e.g. "patient" → "patient")
                $modelType = ucfirst($key);
                break;
            }
        }

        return [$modelType, $modelId];
    }

    /**
     * Check whether the request targets a medical-record route.
     */
    private function isMedicalRecordRoute(Request $request): bool
    {
        $path = $request->path();
        foreach (self::MEDICAL_RECORD_PATTERNS as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }
        return false;
    }
}

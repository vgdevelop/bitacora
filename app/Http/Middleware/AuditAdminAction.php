<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethodSafe() && $request->user() && $response->getStatusCode() < 400) {
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->route()?->getName() ?? 'admin.action',
                'method' => $request->method(),
                'path' => '/'.ltrim($request->path(), '/'),
                'status_code' => $response->getStatusCode(),
                'metadata' => [
                    'model_id' => $request->route('product')?->id
                        ?? $request->route('order')?->id
                        ?? $request->route('category')?->id
                        ?? null,
                ],
            ]);
        }

        return $response;
    }
}

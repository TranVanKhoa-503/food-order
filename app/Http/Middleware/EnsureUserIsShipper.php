<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsShipper
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->isShipper() || $request->user()?->isAdmin(),
            Response::HTTP_FORBIDDEN,
            'Bạn không có quyền truy cập trang dành cho nhân viên giao hàng.',
        );

        return $next($request);
    }
}

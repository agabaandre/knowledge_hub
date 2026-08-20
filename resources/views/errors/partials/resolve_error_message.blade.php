@php
    /**
     * @deprecated Prefer resolving $message / $exception inline in the parent view.
     * Blade @include scopes variables, so assignments here do not reach the parent.
     */
    $resolvedErrorMessage = $message ?? null;
    if (isset($exception) && $exception instanceof \Throwable) {
        $resolvedErrorMessage = $exception->getMessage() ?: $resolvedErrorMessage;
    }
@endphp

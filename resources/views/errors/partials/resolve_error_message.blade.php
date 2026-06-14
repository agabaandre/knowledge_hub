@php
    $resolvedErrorMessage = $message ?? null;
    if (isset($exception) && $exception instanceof \Throwable) {
        $resolvedErrorMessage = $exception->getMessage() ?: $resolvedErrorMessage;
    }
@endphp

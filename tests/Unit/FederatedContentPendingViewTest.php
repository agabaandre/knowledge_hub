<?php

namespace Tests\Unit;

use Tests\TestCase;

class FederatedContentPendingViewTest extends TestCase
{
    public function test_approve_and_reject_submit_the_review_form_without_javascript(): void
    {
        $view = file_get_contents(resource_path('views/admin/federation/pending_content.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('name="action" value="approve"', $view);
        $this->assertStringContainsString('name="action" value="reject"', $view);
        $this->assertMatchesRegularExpression('/<button[^>]+type="submit"[^>]+name="action"[^>]+value="approve"/', $view);
        $this->assertMatchesRegularExpression('/<button[^>]+type="submit"[^>]+name="action"[^>]+value="reject"/', $view);
        $this->assertStringContainsString("@section('scripts')", $view);
        $this->assertStringNotContainsString("@push('scripts')", $view);
    }
}

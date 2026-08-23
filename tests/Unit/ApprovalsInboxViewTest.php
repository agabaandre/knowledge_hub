<?php

namespace Tests\Unit;

use Tests\TestCase;

class ApprovalsInboxViewTest extends TestCase
{
    public function test_inbox_submits_approve_and_reject_without_javascript(): void
    {
        $view = file_get_contents(resource_path('views/admin/approvals/index.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString("route('admin.approvals.review')", $view);
        $this->assertStringContainsString('name="action" value="approve"', $view);
        $this->assertStringContainsString('name="action" value="reject"', $view);
        $this->assertStringContainsString("@section('scripts')", $view);
        $this->assertStringNotContainsString("@push('scripts')", $view);
    }
}

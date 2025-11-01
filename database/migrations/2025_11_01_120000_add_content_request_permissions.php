<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // All permissions used in the admin navigation and throughout the system
        $permissions = [
            // Content Requests (new)
            'view_content_requests' => 'View content requests',
            'manage_content_requests' => 'Manage content requests (process, edit, delete)',
            
            // Publication Metadata (new)
            'delete_publication_metadata' => 'Delete publication metadata fields',
            
            // Dashboard & KPIs
            'view_rcc_dashboard' => 'View RCC dashboard',
            'view_performance' => 'View performance indicators',
            'view_knowledge_hub_dashboard' => 'View knowledge hub dashboard',
            'view_dashboard' => 'View dashboard',
            
            // Publications
            'view_publications' => 'View publications',
            'create_publication' => 'Create publication',
            'update_publications' => 'Update publications',
            'delete_publications' => 'Delete publications',
            'moderate_publication' => 'Moderate publications',
            
            // Content Requests Navigation (already added above)
            
            // Experts
            'manage_experts' => 'Manage experts',
            'view_experts' => 'View experts',
            
            // Facts
            'manage_facts' => 'Manage facts',
            
            // Quotes
            'view_quotes' => 'View quotes',
            'update_quotes' => 'Update quotes',
            
            // Quiz
            'view_quize' => 'View quiz',
            'update_quize' => 'Update quiz',
            
            // Forums
            'view_forumns' => 'View forums',
            'view_forums' => 'View forums',
            'moderate_forum' => 'Moderate forums',
            
            // Events
            'view_events' => 'View events',
            
            // Dropdown Lists - File Types
            'view_file_types' => 'View file types',
            'update_filetypes' => 'Update file types',
            
            // Sources/Authors
            'view_sources' => 'View sources/authors',
            'update_sources' => 'Update sources/authors',
            'add_authors' => 'Add authors',
            
            // Themes
            'view_themes' => 'View themes',
            'update_themes' => 'Update themes',
            
            // Sub Themes
            'view_sub_themes' => 'View sub themes',
            'update_subthemes' => 'Update sub themes',
            
            // Tags
            'view_tags' => 'View tags',
            'update_tags' => 'Update tags',
            
            // FAQs
            'view_faqs' => 'View FAQs',
            
            // Geographical Coverage
            'view_geo_coverage' => 'View geographical coverage',
            'update_geo_coverage' => 'Update geographical coverage',
            
            // Asset Types
            'view_asset_types' => 'View asset types',
            
            // Health Assets
            'view_assets' => 'View health assets',
            'view_ph_assets' => 'View public health assets',
            
            // Privacy Policy
            'view_privacy_policy' => 'View privacy policy',
            'modify_privacy_policy' => 'Modify privacy policy',
            
            // KPIs
            'manage_kpis' => 'Manage KPIs',
            'add_kpis' => 'Add KPIs',
            'manage_rccs' => 'Manage RCCs',
            
            // Users & Permissions
            'add_users' => 'Add users',
            'view_user_audit_log' => 'View user audit log',
            
            // Mailing List
            'view_mailing_list' => 'View mailing list',
            
            // Front Page
            'manage_front_page_slider' => 'Manage front page slider',
            
            // Standard Forms
            'manage_standard_form_lists' => 'Manage standard form lists',
            
            // Workforce
            'view_workforce' => 'View workforce',
        ];

        foreach ($permissions as $name => $description) {
            // Check if permission already exists
            $permission = Permission::where('name', $name)->first();
            if (!$permission) {
                Permission::create([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign all permissions to admin role if it exists
        $adminRole = Role::where('name', 'admin')->orWhere('name', 'Administrator')->first();
        if ($adminRole) {
            foreach (array_keys($permissions) as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't delete existing permissions in down() as they might be in use
        // Only remove the ones we just added if needed
        $newPermissions = [
            'view_content_requests',
            'manage_content_requests',
            'delete_publication_metadata',
        ];

        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }
    }
};



<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SettingsRepository;

class SettingsController extends Controller
{
    private $settingsRepo;

    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepo = $settingsRepo;
    }

    public function index(Request $request){

        $data['settings'] = (Object) $this->settingsRepo->get($request);
        // Load badge types for configuration
        $data['badgeTypes'] = \App\Models\BadgeType::getAllBadgesInOrder();
        return view('admin.settings.index',$data);
    }
  
    public function store(Request $request){

        $saved = $this->settingsRepo->save($request);

        // Update badge configurations if provided
        if ($request->has('badge_ids') && is_array($request->badge_ids)) {
            foreach ($request->badge_ids as $badgeId) {
                $badgeType = \App\Models\BadgeType::find($badgeId);
                if ($badgeType) {
                    // Update threshold
                    if (isset($request->badge_thresholds[$badgeId])) {
                        $badgeType->contribution_threshold = (int)$request->badge_thresholds[$badgeId];
                    }
                    
                    // Update name
                    if (isset($request->badge_names[$badgeId])) {
                        $badgeType->name = $request->badge_names[$badgeId];
                    }
                    
                    // Update description
                    if (isset($request->badge_descriptions[$badgeId])) {
                        $badgeType->description = $request->badge_descriptions[$badgeId];
                    }
                    
                    // Update color
                    if (isset($request->badge_colors[$badgeId])) {
                        $badgeType->badge_color = $request->badge_colors[$badgeId];
                    }
                    
                    // Update active status
                    $badgeType->is_active = isset($request->badge_active[$badgeId]) && $request->badge_active[$badgeId] == '1';
                    
                    $badgeType->save();
                }
            }
        }

        if($saved):
            $data = ['alert-success'=>'Settings saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['alert-danger'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }

    public function clearCache(Request $request){
        
        try {
            $output = [];
            $commands = [
                'cache:clear' => 'Clearing application cache...',
                'config:clear' => 'Clearing configuration cache...',
                'view:clear' => 'Clearing view cache...',
                'route:clear' => 'Clearing route cache...',
            ];
            
            $allOutput = [];
            $allOutput[] = "=== Cache Clear Operation Started ===" . PHP_EOL;
            $allOutput[] = date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
            
            foreach ($commands as $command => $description) {
                $allOutput[] = $description . PHP_EOL;
                try {
                    \Artisan::call($command);
                    $commandOutput = \Artisan::output();
                    if (!empty(trim($commandOutput))) {
                        $allOutput[] = $commandOutput . PHP_EOL;
                    } else {
                        $allOutput[] = "✓ Completed successfully" . PHP_EOL;
                    }
                } catch (\Exception $e) {
                    $allOutput[] = "✗ Error: " . $e->getMessage() . PHP_EOL;
                }
                $allOutput[] = PHP_EOL;
            }
            
            // Also clear the settings cache specifically
            try {
                cache()->forget('settings');
                $allOutput[] = "Clearing settings cache..." . PHP_EOL;
                $allOutput[] = "✓ Settings cache cleared successfully" . PHP_EOL . PHP_EOL;
            } catch (\Exception $e) {
                $allOutput[] = "✗ Error clearing settings cache: " . $e->getMessage() . PHP_EOL . PHP_EOL;
            }
            
            $allOutput[] = "=== Cache Clear Operation Completed ===" . PHP_EOL;
            $allOutput[] = date('Y-m-d H:i:s') . PHP_EOL;
            
            $outputText = implode('', $allOutput);
            
            $message = 'Cache cleared successfully!';
            
            if($request->ajax() || $request->expectsJson()){
                return response()->json([
                    'alert-success' => $message, 
                    'status' => 'success',
                    'output' => $outputText
                ], 200);
            }
            
            return back()->with([
                'alert-success' => $message,
                'cache_output' => $outputText
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error clearing cache: ' . $e->getMessage();
            $errorOutput = "=== Error ===" . PHP_EOL . $e->getMessage() . PHP_EOL . PHP_EOL . $e->getTraceAsString();
            
            if($request->ajax() || $request->expectsJson()){
                return response()->json([
                    'alert-danger' => $errorMessage, 
                    'status' => 'error',
                    'output' => $errorOutput
                ], 500);
            }
            
            return back()->with([
                'alert-danger' => $errorMessage,
                'cache_output' => $errorOutput
            ]);
        }
    }

  
}

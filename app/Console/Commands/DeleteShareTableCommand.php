<?php

namespace App\Console\Commands;

use App\Models\ShareTable;
use App\Models\ShareTableVirtualFile;
use App\Models\VirtualFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteShareTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share-table:delete {id : The ID of the ShareTable to delete} {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forcefully delete a ShareTable and its associated resources (permissions, files, DASH videos) while keeping members.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = $this->argument('id');
        $shareTable = ShareTable::find($id);

        if (! $shareTable) {
            $this->error("ShareTable with ID {$id} not found.");

            return 1;
        }

        if (! $this->option('force') && ! $this->confirm("Are you sure you want to delete ShareTable '{$shareTable->name}' (ID: {$id}) and all its associated resources? This action cannot be undone.")) {
            $this->info('Action cancelled.');

            return 0;
        }

        $this->warn("Starting forceful deletion of ShareTable: {$shareTable->name} (ID: {$id})");

        try {
            DB::transaction(function () use ($shareTable, $id) {
                // 1. Delete SharePermissions
                $permissionCount = $shareTable->shareTablePermission()->count();
                $shareTable->shareTablePermission()->delete();
                $this->info("- Deleted {$permissionCount} permissions.");

                // 2. Get associated VirtualFiles through ShareTableVirtualFile
                $pivotItems = $shareTable->shareTableVirtualFile()->get();
                $fileCount = 0;
                $dashVideoCount = 0;

                foreach ($pivotItems as $pivot) {
                    // Delete VirtualFile (including physical file and its own record)
                    if ($pivot->virtualFile) {
                        $this->line("  Cleaning up VirtualFile: {$pivot->virtualFile->filename} ({$pivot->virtualFile->uuid})");
                        $pivot->virtualFile->deleteEntry();
                        $fileCount++;
                    }

                    // Delete associated DashVideos records
                    if ($pivot->dashVideos) {
                        $this->line("  Cleaning up DashVideo record ID: {$pivot->dashVideos->id}");

                        // If there's a thumbnail associated with the DashVideo, clean it up too
                        if ($pivot->dashVideos->thumbVirtualFile) {
                            $pivot->dashVideos->thumbVirtualFile->deleteEntry();
                        }

                        $pivot->dashVideos->delete();
                        $dashVideoCount++;
                    }

                    // Delete the pivot record itself
                    $pivot->delete();
                }

                // 3. Clean up the entire DashVideos directory for this ShareTable
                $dashVideosFolder = 'DashVideos/'.$id;
                if (Storage::disk('public')->exists($dashVideosFolder)) {
                    $this->line("- Deleting DASH video directory: {$dashVideosFolder}");
                    Storage::disk('public')->deleteDirectory($dashVideosFolder);
                }

                $this->info("- Deleted {$fileCount} virtual files and {$dashVideoCount} DashVideo records.");

                // 4. Finally delete the ShareTable itself
                $shareTable->delete();
                $this->info('- Deleted ShareTable record.');
            });

            $this->info("Successfully deleted ShareTable {$id} and all related resources.");

            return 0;

        } catch (\Exception $e) {
            $this->error('An error occurred during deletion: '.$e->getMessage());
            Log::error('DeleteShareTableCommand Error: '.$e->getMessage(), [
                'share_table_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }
}

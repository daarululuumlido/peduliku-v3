<?php

namespace App\Console\Commands;

use App\Models\Audit;
use Illuminate\Console\Command;

class CleanupAuditLogsCommand extends Command
{
    protected $signature = 'audit:cleanup {--days= : Number of days to keep (default from config)}';

    protected $description = 'Delete audit logs older than retention period';

    public function handle()
    {
        $days = (int) $this->option('days') ?: config('audit.retention_days', 90);

        if ($days <= 0) {
            $this->info('Audit log retention is disabled (0 days). No logs will be deleted.');

            return;
        }

        $cutoffDate = now()->subDays($days);

        $deletedCount = Audit::where('created_at', '<', $cutoffDate)->delete();

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} audit logs older than {$days} days (before {$cutoffDate->toDateTimeString()}).");
        } else {
            $this->info('No old audit logs found to delete.');
        }
    }
}

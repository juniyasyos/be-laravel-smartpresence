<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use App\Jobs\ProcessBackup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Ensure the authenticated user is a SuperAdmin (role_id = 1).
     */
    protected function authorizeSuperAdmin(Request $request): void
    {
        if ($request->user()->role_id !== 1) {
            abort(403, 'Hanya Super Admin yang dapat mengakses fitur cadangan.');
        }
    }

    /**
     * GET /api/backups
     * List all backups (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $query = BackupLog::with('creator:id,username')
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 10);
        $backups = $query->paginate($perPage);

        // Append computed duration attribute
        $backups->getCollection()->transform(function (BackupLog $backup) {
            $backup->append('duration');
            return $backup;
        });

        return response()->json([
            'data' => $backups,
        ]);
    }

    /**
     * GET /api/backups/stats
     * Backup statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $total     = BackupLog::count();
        $completed = BackupLog::where('status', 'completed')->count();
        $failed    = BackupLog::where('status', 'failed')->count();
        $pending   = BackupLog::whereIn('status', ['pending', 'running'])->count();
        $totalSize = BackupLog::where('status', 'completed')->sum('file_size');

        return response()->json([
            'data' => [
                'total'      => $total,
                'completed'  => $completed,
                'failed'     => $failed,
                'pending'    => $pending,
                'total_size' => $totalSize,
            ],
        ]);
    }

    /**
     * POST /api/backup
     * Create a new backup and dispatch the job.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        $type = $request->type;
        $timestamp = now()->format('Y-m-d_His');

        // Generate descriptive name
        $typeLabel = match ($type) {
            'database' => 'Database',
            'files'    => 'Files',
            'full'     => 'Full',
        };

        $name = "{$typeLabel}_Backup_{$timestamp}.zip";

        $backup = BackupLog::create([
            'name'       => $name,
            'type'       => $type,
            'status'     => 'pending',
            'created_by' => $request->user()->id,
        ]);

        // Dispatch the backup job to the queue
        ProcessBackup::dispatch($backup->id);

        return response()->json([
            'message' => 'Backup telah dijadwalkan dan akan segera diproses.',
            'data'    => $backup->load('creator:id,username'),
        ], 201);
    }

    /**
     * GET /api/backup/{id}/download
     * Download a completed backup file.
     */
    public function download(Request $request, int $id): mixed
    {
        $this->authorizeSuperAdmin($request);

        $backup = BackupLog::findOrFail($id);

        if ($backup->status !== 'completed' || !$backup->file_path) {
            return response()->json([
                'message' => 'Backup belum selesai atau file tidak ditemukan.',
            ], 404);
        }

        if (!Storage::exists($backup->file_path)) {
            return response()->json([
                'message' => 'File backup tidak ditemukan di server.',
            ], 404);
        }

        return Storage::download($backup->file_path, $backup->name);
    }

    /**
     * DELETE /api/backup/{id}
     * Delete a backup record and its file.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $backup = BackupLog::findOrFail($id);

        // Delete the file if it exists
        if ($backup->file_path && Storage::exists($backup->file_path)) {
            Storage::delete($backup->file_path);
        }

        $backup->delete();

        return response()->json([
            'message' => 'Backup berhasil dihapus.',
        ]);
    }

    /**
     * POST /api/backup/{id}/cancel
     * Cancel a pending backup.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $backup = BackupLog::findOrFail($id);

        if ($backup->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya backup yang berstatus menunggu (pending) yang dapat dibatalkan.',
            ], 400);
        }

        $backup->update([
            'status' => 'failed',
            'error_message' => 'Dibatalkan oleh pengguna',
        ]);

        return response()->json([
            'message' => 'Backup berhasil dibatalkan.',
        ]);
    }

    /**
     * Clean up old backups (older than 18 months).
     * Called from scheduled Artisan command.
     */
    public static function cleanupOldBackups(): int
    {
        $cutoff = Carbon::now()->subMonths(18);
        $oldBackups = BackupLog::where('created_at', '<', $cutoff)->get();
        $count = 0;

        foreach ($oldBackups as $backup) {
            if ($backup->file_path && Storage::exists($backup->file_path)) {
                Storage::delete($backup->file_path);
            }
            $backup->delete();
            $count++;
        }

        return $count;
    }
}

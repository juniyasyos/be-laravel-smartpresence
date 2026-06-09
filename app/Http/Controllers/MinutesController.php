<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingDocument;
use App\Models\MeetingMinutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Exception;

class MinutesController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // NOTULENSI (Rich-text Quill content)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ambil notulensi + dokumen untuk rapat tertentu.
     * GET /meeting/{meetingId}/minutes
     */
    public function show(string $meetingId)
    {
        try {
            $meeting = Meeting::with(['minutes', 'documents'])->find($meetingId);

            if (!$meeting) {
                return response()->json(['message' => 'Meeting not found'], 404);
            }

            return response()->json([
                'message' => 'Minutes fetched successfully',
                'data'    => [
                    'minutes'   => $meeting->minutes,
                    'documents' => $meeting->documents,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching minutes',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan atau update notulensi rapat (upsert).
     * POST /meeting/{meetingId}/minutes
     */
    public function upsert(Request $request, string $meetingId)
    {
        try {
            $meeting = Meeting::find($meetingId);
            if (!$meeting) {
                return response()->json(['message' => 'Meeting not found'], 404);
            }

            $validated = $request->validate([
                'content'           => 'nullable|string',
                'notulis_name'      => 'nullable|string|max:150',
                'notulis_position'  => 'nullable|string|max:150',
                'director_name'     => 'nullable|string|max:150',
                'director_position' => 'nullable|string|max:150',
            ]);

            $minutes = MeetingMinutes::updateOrCreate(
                ['meeting_id' => $meetingId],
                [
                    'content'           => $validated['content']           ?? null,
                    'notulis_name'      => $validated['notulis_name']      ?? null,
                    'notulis_position'  => $validated['notulis_position']  ?? null,
                    'director_name'     => $validated['director_name']     ?? null,
                    'director_position' => $validated['director_position'] ?? null,
                    'created_by'        => auth()->id() ?? null,
                    'updated_by'        => auth()->id() ?? null,
                ]
            );



            return response()->json([
                'message' => 'Minutes saved successfully',
                'data'    => $minutes,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving minutes',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPLOAD GAMBAR (untuk Quill image handler)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upload gambar dari Quill.js editor.
     * POST /minutes/upload-image
     *
     * Quill mengirim:  { image: <File> }
     * Response:        { url: "http://..." }
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
            ]);

            $path = $request->file('image')->store('minutes/images', 'public');
            $url  = '/storage/' . $path;

            return response()->json(['url' => $url], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Image upload failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOKUMEN RAPAT (file attachment: PDF, Word, dll.)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upload dokumen rapat (undangan, lampiran, dll.).
     * POST /meeting/{meetingId}/documents
     *
     * Body (multipart): { file: <File>, type: "undangan"|"lampiran"|... }
     */
    public function uploadDocument(Request $request, string $meetingId)
    {
        try {
            $meeting = Meeting::find($meetingId);
            if (!$meeting) {
                return response()->json(['message' => 'Meeting not found'], 404);
            }

            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:20480', // max 20MB
                'type' => 'nullable|string|max:50',
            ]);

            $uploadedFile = $request->file('file');
            $path         = $uploadedFile->store('meetings/documents', 'public');

            $document = MeetingDocument::create([
                'meeting_id' => $meetingId,
                'type'       => $request->input('type', 'lampiran'),
                'file_name'  => $uploadedFile->getClientOriginalName(),
                'file_path'  => $path,
                'file_size'  => $uploadedFile->getSize(),
                'mime_type'  => $uploadedFile->getMimeType(),
                'uploaded_by'=> auth()->id() ?? 3,
            ]);



            return response()->json([
                'message' => 'Document uploaded successfully',
                'data'    => array_merge($document->toArray(), [
                    'url' => '/storage/' . $path,
                ]),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Document upload failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus dokumen rapat.
     * DELETE /meeting/{meetingId}/documents/{documentId}
     */
    public function deleteDocument(string $meetingId, string $documentId)
    {
        try {
            $document = MeetingDocument::where('meeting_id', $meetingId)
                ->where('id', $documentId)
                ->first();

            if (!$document) {
                return response()->json(['message' => 'Document not found'], 404);
            }

            // Soft delete — file tetap tersimpan agar bisa di-restore
            $document->delete();

            return response()->json(['message' => 'Document deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting document',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

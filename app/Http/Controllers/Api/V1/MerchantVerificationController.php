<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageKitService;

class MerchantVerificationController extends Controller
{
    use \App\Traits\ApiResponse;
    protected $ImageKitService;

    public function __construct(ImageKitService $ImageKitService)
    {
        $this->ImageKitService = $ImageKitService;
    }

    public function upload(Request $request)
    {
        $user = $request->user();
        
        // Ensure merchant profile exists (Lazy creation if missing)
        if (!$user->merchant) {
            $user->merchant = \App\Models\Merchant::create([
                'user_id' => $user->id,
                'store_name' => $user->name,
                'contact_number' => $user->phone,
                'store_status' => 'inactive',
                'approved' => false,
            ]);
        }
        $merchant = $user->merchant;

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'type' => 'required|string|in:commercial_register,national_id,other',
        ]);

        if ($validator->fails()) {
            return $this->error('خطأ في الملف المرفق', 422, $validator->errors());
        }

        // Upload File
        $path = $this->ImageKitService->upload($request->file('document'));

        // Use the profile (now guaranteed to exist)
        $documents = $merchant->documents ?? [];
        $documents[] = [
            'type' => $request->type,
            'path' => $path,
            'uploaded_at' => now()->toIso8601String(),
            'status' => 'pending'
        ];

        $merchant->update([
            'documents' => $documents,
            'approval_notes' => null, // Clear rejection reason when re-uploading
        ]);

        // After new upload, force status to pending
        $merchant->forceFill(['approved' => false])->save();

        // Transform document paths to URLs
        $transformedDocuments = $this->transformDocumentUrls($documents);

        return $this->success([
            'documents' => $transformedDocuments,
            'status' => 'pending'
        ], 'تم رفع المستند بنجاح');
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $merchant = $user->merchant;
        
        if (!$merchant) {
            return $this->success([
                'is_verified' => false,
                'status' => 'not_submitted',
                'rejection_reason' => null,
                'documents' => [],
            ]);
        }

        // Determine precise status:
        // - approved: merchant->approved is TRUE
        // - rejected: merchant is not approved AND has approval_notes (admin set a rejection reason)
        // - pending:  merchant is not approved, has uploaded documents, but no rejection reason yet
        // - not_submitted: merchant has no documents at all
        $status = 'not_submitted';
        if ($merchant->approved) {
            $status = 'approved';
        } elseif ($merchant->approval_notes) {
            $status = 'rejected';
        } elseif ($merchant->documents && count($merchant->documents) > 0) {
            $status = 'pending';
        }

        return $this->success([
            'is_verified' => (bool) $merchant->approved,
            'status' => $status,
            'rejection_reason' => $merchant->approval_notes,
            'documents' => $this->transformDocumentUrls($merchant->documents ?? []),
        ]);
    }

    private function transformDocumentUrls($documents)
    {
        return array_map(function ($doc) {
            if (isset($doc['path'])) {
                $doc['url'] = \image_url($doc['path']);
            }
            return $doc;
        }, $documents);
    }
}

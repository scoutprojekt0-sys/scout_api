<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ChangeContactStatusRequest;
use App\Http\Requests\Contact\ListContactsRequest;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $toUserId = (int) $validated['to_user_id'];

        $this->authorize('create', [Contact::class, $toUserId]);

        $id = DB::table('contacts')->insertGetId([
            'from_user_id' => (int) $request->user()->id,
            'to_user_id' => $toUserId,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $created = DB::table('contacts')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj gonderildi.',
            'data' => $created,
        ], Response::HTTP_CREATED);
    }

    public function inbox(ListContactsRequest $request): JsonResponse
    {
        $this->authorize('viewInbox', Contact::class);

        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $query = DB::table('contacts')
            ->join('users as sender', 'sender.id', '=', 'contacts.from_user_id')
            ->where('contacts.to_user_id', (int) $request->user()->id)
            ->select([
                'contacts.id',
                'contacts.subject',
                'contacts.message',
                'contacts.status',
                'contacts.created_at',
                'contacts.updated_at',
                'sender.id as sender_id',
                'sender.name as sender_name',
                'sender.role as sender_role',
            ]);

        if (! empty($validated['status'])) {
            $query->where('contacts.status', $validated['status']);
        }

        $sortColumnMap = [
            'created_at' => 'contacts.created_at',
            'status' => 'contacts.status',
        ];

        $inbox = $query->orderBy($sortColumnMap[$sortBy], $sortDir)->paginate($perPage);

        return response()->json([
            'ok' => true,
            'filters' => [
                'status' => $validated['status'] ?? null,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
            'data' => $inbox,
        ]);
    }

    public function sent(ListContactsRequest $request): JsonResponse
    {
        $this->authorize('viewSent', Contact::class);

        $validated = $request->validated();
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $query = DB::table('contacts')
            ->join('users as recipient', 'recipient.id', '=', 'contacts.to_user_id')
            ->where('contacts.from_user_id', (int) $request->user()->id)
            ->select([
                'contacts.id',
                'contacts.subject',
                'contacts.message',
                'contacts.status',
                'contacts.created_at',
                'contacts.updated_at',
                'recipient.id as recipient_id',
                'recipient.name as recipient_name',
                'recipient.role as recipient_role',
            ]);

        if (! empty($validated['status'])) {
            $query->where('contacts.status', $validated['status']);
        }

        $sortColumnMap = [
            'created_at' => 'contacts.created_at',
            'status' => 'contacts.status',
        ];

        $sent = $query->orderBy($sortColumnMap[$sortBy], $sortDir)->paginate($perPage);

        return response()->json([
            'ok' => true,
            'filters' => [
                'status' => $validated['status'] ?? null,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
            'data' => $sent,
        ]);
    }

    public function changeStatus(ChangeContactStatusRequest $request, int $id): JsonResponse
    {
        $contact = Contact::query()->find($id);
        if (! $contact) {
            return response()->json([
                'ok' => false,
                'message' => 'Mesaj bulunamadi.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('changeStatus', $contact);

        DB::table('contacts')
            ->where('id', $id)
            ->update([
                'status' => $request->validated('status'),
                'updated_at' => now(),
            ]);

        $updated = DB::table('contacts')->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'message' => 'Mesaj durumu guncellendi.',
            'data' => $updated,
        ]);
    }
}

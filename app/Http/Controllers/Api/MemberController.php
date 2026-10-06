<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\LayananMember;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function __construct(
        private readonly LayananMember $layananMember,
    ) {}

    /**
     * Menampilkan daftar member.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Daftar member',
            'data' => $this->layananMember->semua(),
        ]);
    }
}

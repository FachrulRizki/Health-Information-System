<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffController extends Controller
{
    /** Non-doctor roles eligible for staff management (Req 19.2) */
    private const STAFF_ROLES = ['perawat', 'farmasi', 'kasir', 'petugas_pendaftaran', 'manajemen'];

    public function index(Request $request): View
    {
        $query = User::whereIn('role', self::STAFF_ROLES);

        if ($q = $request->input('q')) {
            $query->where('username', 'like', "%{$q}%");
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $staff = $query->latest()->paginate(15)->withQueryString();

        return view('master.staff.index', [
            'staff'      => $staff,
            'staffRoles' => self::STAFF_ROLES,
        ]);
    }

    public function create(): View
    {
        return view('master.staff.form', [
            'member'     => null,
            'staffRoles' => self::STAFF_ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username'   => ['required', 'string', 'max:100', Rule::unique('users', 'username')],
            'password'   => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role'       => ['required', Rule::in(self::STAFF_ROLES)],
            'unit_kerja' => ['nullable', 'string', 'max:100'],
            'is_active'  => ['boolean'],
        ]);

        User::create([
            'username'   => $data['username'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('master.staff.index')
            ->with('success', 'Data petugas berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $member = User::whereIn('role', self::STAFF_ROLES)->findOrFail($id);

        return view('master.staff.form', [
            'member'     => $member,
            'staffRoles' => self::STAFF_ROLES,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $member = User::whereIn('role', self::STAFF_ROLES)->findOrFail($id);

        $data = $request->validate([
            'username'   => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($id)],
            'password'   => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role'       => ['required', Rule::in(self::STAFF_ROLES)],
            'unit_kerja' => ['nullable', 'string', 'max:100'],
            'is_active'  => ['boolean'],
        ]);

        $updateData = [
            'username'  => $data['username'],
            'role'      => $data['role'],
            'is_active' => $request->boolean('is_active', false),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $member->update($updateData);

        return redirect()->route('master.staff.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $member = User::whereIn('role', self::STAFF_ROLES)->findOrFail($id);
        $member->update(['is_active' => false]);

        return redirect()->route('master.staff.index')
            ->with('success', 'Petugas berhasil dinonaktifkan.');
    }
}

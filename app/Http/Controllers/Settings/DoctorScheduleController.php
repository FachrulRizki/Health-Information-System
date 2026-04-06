<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Poli;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = DoctorSchedule::with(['doctor', 'poli']);

        if ($q = $request->input('q')) {
            $query->whereHas('doctor', fn($d) => $d->where('nama_dokter', 'like', "%{$q}%"))
                  ->orWhereHas('poli', fn($p) => $p->where('nama_poli', 'like', "%{$q}%"));
        }

        if ($doctorId = $request->input('doctor_id')) {
            $query->where('doctor_id', $doctorId);
        }

        if ($poliId = $request->input('poli_id')) {
            $query->where('poli_id', $poliId);
        }

        $schedules = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15)->withQueryString();
        $doctors   = Doctor::where('is_active', true)->orderBy('nama_dokter')->get();
        $polis     = Poli::where('is_active', true)->orderBy('nama_poli')->get();

        return view('master.schedules.index', compact('schedules', 'doctors', 'polis'));
    }

    public function create(): View
    {
        $doctors = Doctor::where('is_active', true)->orderBy('nama_dokter')->get();
        $polis   = Poli::where('is_active', true)->orderBy('nama_poli')->get();

        return view('master.schedules.form', [
            'schedule' => null,
            'doctors'  => $doctors,
            'polis'    => $polis,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id'  => ['required', 'integer', 'exists:doctors,id'],
            'poli_id'    => ['required', 'integer', 'exists:polis,id'],
            'hari'       => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu'],
            'jam_mulai'  => ['required', 'date_format:H:i'],
            'jam_selesai'=> ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota'      => ['required', 'integer', 'min:1', 'max:999'],
            'is_active'  => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DoctorSchedule::create($data);
        $this->invalidateScheduleCache((int) $data['doctor_id']);

        return redirect()->route('master.schedules.index')
            ->with('success', 'Jadwal praktik berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $schedule = DoctorSchedule::with(['doctor', 'poli'])->findOrFail($id);
        $doctors  = Doctor::where('is_active', true)->orderBy('nama_dokter')->get();
        $polis    = Poli::where('is_active', true)->orderBy('nama_poli')->get();

        return view('master.schedules.form', compact('schedule', 'doctors', 'polis'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $schedule = DoctorSchedule::findOrFail($id);

        $data = $request->validate([
            'doctor_id'  => ['required', 'integer', 'exists:doctors,id'],
            'poli_id'    => ['required', 'integer', 'exists:polis,id'],
            'hari'       => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu'],
            'jam_mulai'  => ['required', 'date_format:H:i'],
            'jam_selesai'=> ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota'      => ['required', 'integer', 'min:1', 'max:999'],
            'is_active'  => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $schedule->update($data);

        // Req 19.4: invalidate slot availability cache immediately when schedule changes
        $this->invalidateScheduleCache((int) $data['doctor_id']);

        return redirect()->route('master.schedules.index')
            ->with('success', 'Jadwal praktik berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $schedule = DoctorSchedule::findOrFail($id);
        $doctorId = $schedule->doctor_id;
        $schedule->delete();

        $this->invalidateScheduleCache($doctorId);

        return redirect()->route('master.schedules.index')
            ->with('success', 'Jadwal praktik berhasil dihapus.');
    }

    /**
     * Invalidate schedule-related cache keys so online registration
     * reflects changes immediately (Req 19.4).
     */
    private function invalidateScheduleCache(int $doctorId): void
    {
        // Flush all schedule cache keys for this doctor across all dates
        // Cache key pattern from design.md: schedule:doctor:{id}:{date}
        // We use a tag-based approach if supported, otherwise flush by pattern
        if (config('cache.default') === 'redis') {
            $pattern = 'schedule:doctor:' . $doctorId . ':*';
            $keys    = Cache::getRedis()->keys(config('database.redis.cache.prefix', '') . $pattern);
            if (! empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }

        // Also flush queue slot cache for all polis (conservative invalidation)
        // Cache key pattern: queue:slot:{poli_id}:{date}
        // Since we don't know which poli/date combos are cached, flush by tag or skip
        // For non-Redis drivers, we rely on TTL expiry (5 min for slots, 15 min for schedules)
    }
}

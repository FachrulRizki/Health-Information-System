<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MasterDataController extends Controller
{
    private string $model;
    private string $entity;
    private string $requestClass;
    private string $routePrefix;
    private string $viewLabel;

    private static array $entityMap = [
        'polis' => [
            'model'   => \App\Models\Poli::class,
            'request' => \App\Http\Requests\MasterData\PoliRequest::class,
            'route'   => 'master.polis',
            'label'   => 'Poli',
        ],
        'drugs' => [
            'model'   => \App\Models\Drug::class,
            'request' => \App\Http\Requests\MasterData\DrugRequest::class,
            'route'   => 'master.drugs',
            'label'   => 'Obat',
        ],
        'doctors' => [
            'model'   => \App\Models\Doctor::class,
            'request' => \App\Http\Requests\MasterData\DoctorRequest::class,
            'route'   => 'master.doctors',
            'label'   => 'Dokter',
        ],
        'icd10-codes' => [
            'model'   => \App\Models\Icd10Code::class,
            'request' => \App\Http\Requests\MasterData\IcdCodeRequest::class,
            'route'   => 'master.icd10-codes',
            'label'   => 'Kode ICD-10',
        ],
        'icd9cm-codes' => [
            'model'   => \App\Models\Icd9cmCode::class,
            'request' => \App\Http\Requests\MasterData\IcdCodeRequest::class,
            'route'   => 'master.icd9cm-codes',
            'label'   => 'Kode ICD-9 CM',
        ],
        'rooms' => [
            'model'   => \App\Models\Room::class,
            'request' => \App\Http\Requests\MasterData\RoomRequest::class,
            'route'   => 'master.rooms',
            'label'   => 'Kamar',
        ],
        'action-masters' => [
            'model'   => \App\Models\ActionMaster::class,
            'request' => \App\Http\Requests\MasterData\ActionMasterRequest::class,
            'route'   => 'master.action-masters',
            'label'   => 'Tindakan',
        ],
    ];

    public function __construct(
        private readonly MasterDataService $service,
        string $entity
    ) {
        $config = self::$entityMap[$entity] ?? null;
        if (! $config) {
            abort(404, "Entity '{$entity}' tidak ditemukan.");
        }
        $this->entity       = $entity;
        $this->model        = $config['model'];
        $this->requestClass = $config['request'];
        $this->routePrefix  = $config['route'];
        $this->viewLabel    = $config['label'];
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['q']);
        $items   = $this->service->getAll($this->model, $filters);

        return view('master.index', [
            'items'       => $items,
            'entity'      => $this->entity,
            'routePrefix' => $this->routePrefix,
            'label'       => $this->viewLabel,
            'q'           => $filters['q'] ?? '',
        ]);
    }

    public function create(): View
    {
        return view('master.form', [
            'record'      => null,
            'entity'      => $this->entity,
            'routePrefix' => $this->routePrefix,
            'label'       => $this->viewLabel,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $formRequest = app($this->requestClass);
        $formRequest->setContainer(app())->setRedirector(app('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        // Auto-generate kode for doctors
        if ($this->entity === 'doctors') {
            $lastDoctor = \App\Models\Doctor::orderByDesc('id')->first();
            $nextNum    = $lastDoctor ? ((int) substr($lastDoctor->kode_dokter, 4)) + 1 : 1;
            $data['kode_dokter'] = 'DOK-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        }

        // Handle doctor specialization text input
        if ($this->entity === 'doctors' && !empty($request->input('spesialisasi_text'))) {
            $spec = \App\Models\Specialization::firstOrCreate(
                ['nama' => trim($request->input('spesialisasi_text'))]
            );
            $data['specialization_id'] = $spec->id;
        }
        if ($this->entity === 'doctors' && !empty($request->input('sub_spesialisasi_text'))) {
            $subSpec = \App\Models\SubSpecialization::firstOrCreate(
                ['nama' => trim($request->input('sub_spesialisasi_text'))]
            );
            $data['sub_specialization_id'] = $subSpec->id;
        }

        // Auto-generate kode for drugs
        if ($this->entity === 'drugs') {
            $lastDrug = \App\Models\Drug::orderByDesc('id')->first();
            $nextNum  = $lastDrug ? ((int) substr($lastDrug->kode, 4)) + 1 : 1;
            $data['kode'] = 'OBT-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

            $drug = $this->service->create($this->model, $data);

            $initialStock = (float) $request->input('initial_stock', 0);
            if ($initialStock > 0) {
                \App\Models\DrugStock::create([
                    'drug_id'       => $drug->id,
                    'quantity'      => $initialStock,
                    'minimum_stock' => (float) $request->input('minimum_stock', 10),
                    'expiry_date'   => $request->input('expiry_date') ?: null,
                    'batch_number'  => $request->input('batch_number') ?: null,
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('success', "{$this->viewLabel} berhasil ditambahkan.");
        }

        $this->service->create($this->model, $data);

        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', "{$this->viewLabel} berhasil ditambahkan.");
    }

    public function edit(int $id): View
    {
        $record = $this->model::findOrFail($id);

        // Load stocks for drugs
        if ($this->entity === 'drugs') {
            $record->load('stocks');
        }

        return view('master.form', [
            'record'      => $record,
            'entity'      => $this->entity,
            'routePrefix' => $this->routePrefix,
            'label'       => $this->viewLabel,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $formRequest = app($this->requestClass);
        $formRequest->setContainer(app())->setRedirector(app('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        // Handle doctor specialization text input on update
        if ($this->entity === 'doctors' && !empty($request->input('spesialisasi_text'))) {
            $spec = \App\Models\Specialization::firstOrCreate(
                ['nama' => trim($request->input('spesialisasi_text'))]
            );
            $data['specialization_id'] = $spec->id;
        }
        if ($this->entity === 'doctors' && !empty($request->input('sub_spesialisasi_text'))) {
            $subSpec = \App\Models\SubSpecialization::firstOrCreate(
                ['nama' => trim($request->input('sub_spesialisasi_text'))]
            );
            $data['sub_specialization_id'] = $subSpec->id;
        }

        $this->service->update($this->model, $id, $data);

        // Handle drug stock update
        if ($this->entity === 'drugs') {
            $initialStock = (float) $request->input('initial_stock', 0);
            \App\Models\DrugStock::updateOrCreate(
                ['drug_id' => $id],
                [
                    'quantity'      => $initialStock,
                    'minimum_stock' => (float) $request->input('minimum_stock', 10),
                    'expiry_date'   => $request->input('expiry_date') ?: null,
                    'batch_number'  => $request->input('batch_number') ?: null,
                ]
            );
        }

        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', "{$this->viewLabel} berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->delete($this->model, $id);
            return redirect()->route("{$this->routePrefix}.index")
                ->with('success', "{$this->viewLabel} berhasil dihapus.");
        } catch (RuntimeException $e) {
            return redirect()->route("{$this->routePrefix}.index")
                ->with('error', $e->getMessage());
        }
    }
}

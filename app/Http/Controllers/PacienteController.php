<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Services\PacienteService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PacienteRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PacienteController extends Controller
{
    public function __construct(
        protected PacienteService $pacienteService
    ) {}

    public function index(): Response
    {
        $pacientes = $this->pacienteService->getAllByFonoaudiologo(Auth::id());
        
        return Inertia::render('Pacientes/Index', [
            'pacientes' => $pacientes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Pacientes/Create', $this->pacienteService->getFormData());
    }

    public function store(PacienteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['fonoaudiologo_id'] = Auth::id();

        $this->pacienteService->create($data);

        return redirect()->route('pacientes.index')->with('success', 'Paciente cadastrado com sucesso!');
    }

    public function edit(Paciente $paciente): Response
    {
        $this->authorizeAccess($paciente);

        return Inertia::render('Pacientes/Edit', $this->pacienteService->getFormData($paciente));
    }

    public function update(PacienteRequest $request, Paciente $paciente): RedirectResponse
    {
        $this->authorizeAccess($paciente);

        $this->pacienteService->update($paciente->id, $request->validated());

        return redirect()->route('pacientes.index')->with('success', 'Paciente atualizado com sucesso!');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $this->authorizeAccess($paciente);

        $this->pacienteService->delete($paciente->id);

        return redirect()->route('pacientes.index')->with('success', 'Paciente removido com sucesso!');
    }

    private function authorizeAccess(Paciente $paciente): void
    {
        if ($paciente->fonoaudiologo_id !== Auth::id()) {
            abort(403);
        }
    }
}
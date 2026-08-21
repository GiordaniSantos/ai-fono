<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescricaoRequest;
use App\Models\Prescricao;
use App\Services\PrescricaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PrescricaoController extends Controller
{
    public function __construct(
        protected PrescricaoService $prescricaoService
    ) {}

    public function index(): Response
    {
        $prescricoes = $this->prescricaoService->getAllByFonoaudiologo(Auth::id());

        return Inertia::render('Prescricoes/Index', [
            'prescricoes' => $prescricoes,
        ]);
    }

    public function create(Request $request): Response
    {
        $formData = $this->prescricaoService->getFormData();
        $formData['selected_paciente_id']  = $request->query('paciente_id', '');
        $formData['selected_exercicio_id'] = $request->query('exercicio_id', '');

        return Inertia::render('Prescricoes/Create', $formData);
    }

    public function store(PrescricaoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['fonoaudiologo_id'] = Auth::id();
        $data['realizada'] = $request->boolean('realizada', false);

        $this->prescricaoService->create($data);

        return redirect()->route('prescricoes.index')->with('success', 'Exercício prescrito com sucesso!');
    }

    public function edit(Prescricao $prescricao): Response
    {
        $this->authorizeAccess($prescricao);

        return Inertia::render('Prescricoes/Edit', $this->prescricaoService->getFormData($prescricao));
    }

    public function update(PrescricaoRequest $request, Prescricao $prescricao): RedirectResponse
    {
        $this->authorizeAccess($prescricao);

        $data = $request->validated();
        $data['realizada'] = $request->boolean('realizada');

        $this->prescricaoService->update($prescricao->id, $data);

        return redirect()->route('prescricoes.index')->with('success', 'Prescrição atualizada com sucesso!');
    }

    public function destroy(Prescricao $prescricao): RedirectResponse
    {
        $this->authorizeAccess($prescricao);

        $this->prescricaoService->delete($prescricao->id);

        return redirect()->route('prescricoes.index')->with('success', 'Prescrição removida com sucesso!');
    }

    public function toggleRealizada(Prescricao $prescricao): RedirectResponse
    {
        $this->authorizeAccess($prescricao);

        $this->prescricaoService->toggleRealizada($prescricao);

        return back()->with('success', 'Status da prescrição atualizado!');
    }

    private function authorizeAccess(Prescricao $prescricao): void
    {
        if ((int) $prescricao->fonoaudiologo_id !== (int) Auth::id()) {
            abort(403);
        }
    }
}

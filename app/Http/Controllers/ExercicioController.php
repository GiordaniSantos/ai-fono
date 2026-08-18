<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExercicioRequest;
use App\Models\Categoria;
use App\Models\Exercicio;
use App\Models\Paciente;
use App\Services\ExercicioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExercicioController extends Controller
{
    public function __construct(
        protected ExercicioService $exercicioService
    ) {}

    public function index(): Response
    {
        $fonoId = Auth::id();

        $exercicios = $this->exercicioService->getAllByFonoaudiologo($fonoId);
        $pacientes = Paciente::where('fonoaudiologo_id', $fonoId)->select('id', 'nome')->orderBy('nome')->get();
        $categorias = Categoria::where('fonoaudiologo_id', $fonoId)->select('id', 'nome')->orderBy('nome')->get();

        return Inertia::render('Exercicios/Index', [
            'exercicios' => $exercicios,
            'pacientes' => $pacientes,
            'categorias' => $categorias,
        ]);
    }

    public function create(Request $request): Response
    {
        $formData = $this->exercicioService->getFormData();
        $formData['selected_paciente_id'] = $request->query('paciente_id');

        return Inertia::render('Exercicios/Create', $formData);
    }

    public function store(ExercicioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['fonoaudiologo_id'] = Auth::id();

        $this->exercicioService->create($data);

        return redirect()->route('exercicios.index')->with('success', 'Exercício cadastrado com sucesso!');
    }

    public function edit(Exercicio $exercicio): Response
    {
        $this->authorizeAccess($exercicio);

        return Inertia::render('Exercicios/Edit', $this->exercicioService->getFormData($exercicio));
    }

    public function update(ExercicioRequest $request, Exercicio $exercicio): RedirectResponse
    {
        $this->authorizeAccess($exercicio);

        $this->exercicioService->update($exercicio->id, $request->validated());

        return redirect()->route('exercicios.index')->with('success', 'Exercício atualizado com sucesso!');
    }

    public function destroy(Exercicio $exercicio): RedirectResponse
    {
        $this->authorizeAccess($exercicio);

        $this->exercicioService->delete($exercicio->id);

        return redirect()->route('exercicios.index')->with('success', 'Exercício removido com sucesso!');
    }

    private function authorizeAccess(Exercicio $exercicio): void
    {
        if ($exercicio->fonoaudiologo_id !== Auth::id()) {
            abort(403);
        }
    }
}
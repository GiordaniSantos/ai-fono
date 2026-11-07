<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExercicioRequest;
use App\Models\Categoria;
use App\Models\Exercicio;
use App\Models\Paciente;
use App\Services\ExercicioService;
use App\Services\GeminiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class ExercicioController extends Controller
{
    public function __construct(
        protected ExercicioService $exercicioService,
        protected GeminiService $geminiService
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

    public function gerarDescricaoIa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'objetivo' => ['required', 'string', 'max:255'],
            'paciente_id' => ['nullable', 'exists:pacientes,id'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'idade' => ['nullable', 'numeric', 'min:0', 'max:120'],
            'diagnostico' => ['nullable', 'string', 'max:500'],
            'interesse' => ['nullable', 'string', 'max:255'],
            'tipo_conteudo' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $descricao = $this->geminiService->gerarDescricaoIa($validated, Auth::id());

            return response()->json([
                'sucesso'   => true,
                'descricao' => $descricao,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Erro ao comunicar com a IA: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function authorizeAccess(Exercicio $exercicio): void
    {
        if ($exercicio->fonoaudiologo_id !== Auth::id()) {
            abort(403);
        }
    }
}

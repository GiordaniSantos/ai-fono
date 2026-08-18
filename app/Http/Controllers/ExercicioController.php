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
use Carbon\Carbon;
use Gemini;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;

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

    public function gerarDescricaoIa(Request $request): JsonResponse
    {
        $request->validate([
            'objetivo'      => ['required', 'string', 'max:255'],
            'paciente_id'   => ['nullable', 'exists:pacientes,id'],
            'categoria_id'  => ['nullable', 'exists:categorias,id'],
            'idade'         => ['nullable', 'numeric', 'min:0', 'max:120'],
            'diagnostico'   => ['nullable', 'string', 'max:500'],
            'interesse'     => ['nullable', 'string', 'max:255'],
            'tipo_conteudo' => ['nullable', 'string', 'max:50'],
        ]);

        $objetivo      = $request->input('objetivo');
        $pacienteId    = $request->input('paciente_id');
        $categoriaId   = $request->input('categoria_id');
        $interesse     = $request->input('interesse');
        $idade         = $request->input('idade');
        $diagnostico   = $request->input('diagnostico');
        $tipoConteudo  = $request->input('tipo_conteudo', 'frases');
        $categoriaNome = null;

        if ($categoriaId) {
            $categoriaNome = Categoria::where('fonoaudiologo_id', Auth::id())->find($categoriaId)?->nome;
        }

        if ($pacienteId) {
            $paciente = Paciente::where('fonoaudiologo_id', Auth::id())->find($pacienteId);
            if ($paciente) {
                if (!$idade && $paciente->data_nascimento) {
                    $idade = Carbon::parse($paciente->data_nascimento)->age;
                }
                if (!$diagnostico) {
                    $diagnostico = $paciente->diagnostico;
                }
            }
        }

        $formatosMap = [
            'frases'         => 'Frases curtas e motivadoras',
            'trava_linguas'  => 'Trava-línguas divertido e focado no alvo',
            'historinha'     => 'História curta e lúdica (1 a 2 parágrafos)',
            'lista_palavras' => 'Lista de palavras selecionadas para treino',
            'livre'          => 'Texto adaptado livremente ao objetivo',
        ];
        $formatoInstrucao = $formatosMap[$tipoConteudo] ?? $formatosMap['frases'];

        $prompt = "Você é um assistente fonoaudiológico especializado em criação de material textual para treino de fala e linguagem.\n";
        $prompt .= "Gere EXCLUSIVAMENTE o conteúdo no formato: {$formatoInstrucao}.\n\n";

        $prompt .= "Parâmetros Clínicos:\n";
        $prompt .= "- Alvo/Objetivo Fonético ou Terapêutico: {$objetivo}\n";
        if ($categoriaNome) $prompt .= "- Categoria/Área: {$categoriaNome}\n";
        if ($idade)         $prompt .= "- Idade do Paciente: {$idade} anos\n";
        if ($diagnostico)   $prompt .= "- Diagnóstico/Queixa: {$diagnostico}\n";
        if ($interesse)     $prompt .= "- Tema de Interesse para Contextualização Lúdica: {$interesse}\n";

        $prompt .= "\nRegras Obrigatórias:";
        $prompt .= "\n1. Escreva ESTRITAMENTE no formato solicitado ({$formatoInstrucao}).";
        if ($interesse) {
            $prompt .= "\n2. Insira o tema de interesse '{$interesse}' na narrativa ou nas frases para garantir alto engajamento.";
        }
        $prompt .= "\n3. NÃO inclua repetições, metas, séries, cronômetros ou contagens (ex: NÃO escreva 'faça 3x', 'repita 10 vezes').";
        $prompt .= "\n4. NÃO inclua introduções, saudações ou explicações (retorne apenas o texto do exercício pronto).";
        $prompt .= "\n5. Adeque o vocabulário à idade " . ($idade ? "({$idade} anos)" : "do paciente") . ".";

        try {
            $client = Gemini::factory()
                ->withApiKey(env('GEMINI_API_KEY'))
                ->withHttpClient(new GuzzleClient(['verify' => false]))
                ->make();

            $result = $client->generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);

            return response()->json([
                'sucesso'   => true,
                'descricao' => trim($result->text()),
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

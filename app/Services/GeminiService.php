<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Paciente;
use Carbon\Carbon;
use Gemini;
use GuzzleHttp\Client as GuzzleClient;

class GeminiService
{
    public function gerarDescricaoIa(array $data, int $fonoId): string
    {
        $objetivo = $data['objetivo'] ?? null;
        $pacienteId = $data['paciente_id'] ?? null;
        $categoriaId = $data['categoria_id'] ?? null;
        $interesse = $data['interesse'] ?? null;
        $idade = $data['idade'] ?? null;
        $diagnostico = $data['diagnostico'] ?? null;
        $tipoConteudo = $data['tipo_conteudo'] ?? 'frases';
        $categoriaNome = null;

        if ($categoriaId) {
            $categoriaNome = Categoria::where('fonoaudiologo_id', $fonoId)->find($categoriaId)?->nome;
        }

        if ($pacienteId) {
            $paciente = Paciente::where('fonoaudiologo_id', $fonoId)->find($pacienteId);
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
            'frases' => 'Frases curtas e motivadoras',
            'trava_linguas' => 'Trava-línguas divertido e focado no alvo',
            'historinha' => 'História curta e lúdica (1 a 2 parágrafos)',
            'lista_palavras' => 'Lista de palavras selecionadas para treino',
            'livre' => 'Texto adaptado livremente ao objetivo',
        ];
        $formatoInstrucao = $formatosMap[$tipoConteudo] ?? $formatosMap['frases'];

        $prompt = "Você é um assistente fonoaudiológico especializado em criação de material textual para treino de fala e linguagem.\n";
        $prompt .= "Gere EXCLUSIVAMENTE o conteúdo no formato: {$formatoInstrucao}.\n\n";

        $prompt .= "Parâmetros Clínicos:\n";
        $prompt .= "Alvo/Objetivo Fonético ou Terapêutico: {$objetivo}\n";
        if ($categoriaNome) $prompt .= "Categoria/Área: {$categoriaNome}\n";
        if ($idade) $prompt .= "Idade do Paciente: {$idade} anos\n";
        if ($diagnostico) $prompt .= "Diagnóstico/Queixa: {$diagnostico}\n";
        if ($interesse) $prompt .= "Tema de Interesse para Contextualização Lúdica: {$interesse}\n";

        $prompt .= "\nRegras Obrigatórias:";
        $prompt .= "\n1. Escreva ESTRITAMENTE no formato solicitado ({$formatoInstrucao}).";
        if ($interesse) {
            $prompt .= "\n2. Insira o tema de interesse '{$interesse}' na narrativa ou nas frases para garantir alto engajamento.";
        }
        $prompt .= "\n3. NÃO inclua repetições, metas, séries, cronômetros ou contagens (ex: NÃO escreva 'faça 3x', 'repita 10 vezes').";
        $prompt .= "\n4. NÃO inclua introduções, saudações ou explicações (retorne apenas o texto do exercício pronto).";
        $prompt .= "\n5. Adeque o vocabulário à idade " . ($idade ? "({$idade} anos)" : "do paciente") . ".";

        $client = Gemini::factory()->withApiKey(env('GEMINI_API_KEY'))->withHttpClient(new GuzzleClient(['verify' => false]))->make();

        $result = $client->generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);

        return trim($result->text());
    }
}

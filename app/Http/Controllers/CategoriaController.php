<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CategoriaController extends Controller
{
    public function index(): Response
    {
        $categorias = Categoria::where('fonoaudiologo_id', Auth::id())->withCount('exercicios')->latest()->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
        ]);
    }

    public function store(CategoriaRequest $request): RedirectResponse
    {
        Categoria::create([
            'fonoaudiologo_id' => Auth::id(),
            'nome' => $request->validated('nome'),
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria cadastrada com sucesso!');
    }

    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $this->authorizeAccess($categoria);

        $categoria->update($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        $this->authorizeAccess($categoria);

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoria removida com sucesso!');
    }

    private function authorizeAccess(Categoria $categoria): void
    {
        if ($categoria->fonoaudiologo_id !== Auth::id()) {
            abort(403);
        }
    }
}
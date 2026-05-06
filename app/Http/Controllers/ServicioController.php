<?php

namespace App\Http\Controllers;

use App\Helpers\CategoriaHelper;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::with(['user.resenas', 'user.perfilProfesional'])
            ->whereHas('user', function ($q) {
                $q->where('role_id', 2)
                    ->whereNotNull('email_verified_at')
                    ->whereHas('perfilProfesional', function ($p) {
                        $p->whereNotNull('descripcion')
                            ->whereNotNull('whatsapp')
                            ->where('descripcion', '!=', '')
                            ->where('whatsapp', '!=', '')
                            ->where('en_vacaciones', false);
                    })
                    ->whereHas('servicios');
            });

        if ($request->filled('categoria') && $request->categoria !== 'todos') {
            $arbol = CategoriaHelper::arbol();
            $padre = $request->categoria;
            if (isset($arbol[$padre])) {
                $subs = array_keys($arbol[$padre]['subs']);
                $query->where(function ($q) use ($padre, $subs) {
                    $q->where('categoria', $padre)
                        ->orWhereIn('categoria', $subs)
                        ->orWhereIn('subcategoria', $subs);
                });
            } else {
                $query->where(function ($q) use ($padre) {
                    $q->where('categoria', $padre)
                        ->orWhere('subcategoria', $padre);
                });
            }
        }

        if ($request->filled('subcategoria')) {
            $query->where(function ($q) use ($request) {
                $q->where('subcategoria', $request->subcategoria)
                    ->orWhere('categoria', $request->subcategoria);
            });
        }

        if ($request->filled('buscar')) {
            $buscar = strtolower(trim($request->buscar));
            $sinonimos = CategoriaHelper::sinonimos();
            $categoriasEncontradas = [];
            foreach ($sinonimos as $subSlug => $palabras) {
                foreach ($palabras as $palabra) {
                    if (str_contains($buscar, strtolower($palabra)) ||
                        str_contains(strtolower($palabra), $buscar)) {
                        $categoriasEncontradas[] = $subSlug;
                        break;
                    }
                }
            }
            $query->where(function ($q) use ($buscar, $categoriasEncontradas) {
                $q->where('titulo', 'like', "%$buscar%")
                    ->orWhere('descripcion', 'like', "%$buscar%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$buscar%"));
                if (! empty($categoriasEncontradas)) {
                    $q->orWhereIn('categoria', $categoriasEncontradas)
                        ->orWhereIn('subcategoria', $categoriasEncontradas);
                }
            });
        }

        if ($request->filled('calificacion')) {
            $calificacion = $request->calificacion;
            if ($calificacion === 'sin') {
                $query->whereDoesntHave('user.resenas');
            } elseif (is_numeric($calificacion)) {
                $query->whereHas('user', function ($u) use ($calificacion) {
                    $u->whereRaw(
                        '(SELECT AVG(calificacion) FROM resenas WHERE resenas.profesional_id = users.id) >= ?',
                        [$calificacion]
                    );
                });
            }
        }

        $todosLosServicios = $query->latest()->get();

        $serviciosFiltrados = $todosLosServicios
            ->groupBy(function ($servicio) {
                $cat = $servicio->subcategoria ?: $servicio->categoria;
                $padre = CategoriaHelper::padreDeSub($cat) ?? $servicio->categoria;

                return $servicio->user_id.'_'.$padre;
            })
            ->map(function ($grupo) {
                return $grupo->sortByDesc(function ($s) {
                    return $s->user->promedioCalificacion();
                })->first();
            })
            ->values();

        $perPage = 12;
        $page = $request->input('page', 1);
        $total = $serviciosFiltrados->count();
        $items = $serviciosFiltrados->slice(($page - 1) * $perPage, $perPage)->values();

        $servicios = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categorias = CategoriaHelper::arbol();

        return view('servicios.index', compact('servicios', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'categoria' => 'required',
            'subcategoria' => 'nullable',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|max:2048',
        ]);

        $fotosRutas = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $fotosRutas[] = $foto->store('servicios', 'public');
            }
        }

        Servicio::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'subcategoria' => $request->subcategoria,
            'duracion' => $request->duracion ?? 60,
            'foto' => $fotosRutas[0] ?? null,
            'fotos' => $fotosRutas ?: null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Servicio creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);
        if ($servicio->user_id != auth()->id()) {
            abort(403);
        }

        if (! auth()->user()->hasVerifiedEmail()) {
            return redirect()->back()->with('error', 'Debes verificar tu correo electrónico antes de publicar servicios.');
        }

        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
            'categoria' => 'required',
            'subcategoria' => 'nullable',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|max:2048',
        ]);

        $datos = [
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'subcategoria' => $request->subcategoria,
            'duracion' => $request->duracion ?? $servicio->duracion,
        ];

        if ($request->hasFile('fotos')) {
            // Eliminar fotos anteriores
            if ($servicio->fotos) {
                foreach ($servicio->fotos as $fotoVieja) {
                    \Storage::disk('public')->delete($fotoVieja);
                }
            }
            $fotosRutas = [];
            foreach ($request->file('fotos') as $foto) {
                $fotosRutas[] = $foto->store('servicios', 'public');
            }
            $datos['foto'] = $fotosRutas[0];
            $datos['fotos'] = $fotosRutas;
        }

        $servicio->update($datos);

        return redirect()->back()->with('success', 'Servicio actualizado');
    }

    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        if ($servicio->user_id != auth()->id()) {
            abort(403);
        }
        $servicio->delete();

        return redirect()->back()->with('success', 'Servicio eliminado');
    }
}

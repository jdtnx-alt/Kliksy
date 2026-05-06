<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Resena;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsuarios = User::whereIn('role_id', [1, 2])->count();
        $totalClientes = User::where('role_id', 1)->count();
        $totalProfesionales = User::where('role_id', 2)->count();
        $totalServicios = Servicio::count();
        $totalResenas = Resena::count();
        $totalReportes = Reporte::where('estado', 'pendiente')->count();
        // (Solicitudes eliminadas, usamos Reservas)

        // Servicios por categoría
        $serviciosPorCategoria = Servicio::selectRaw('categoria, count(*) as total')
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Usuarios registrados últimos 6 meses
        $usuariosPorMes = User::whereIn('role_id', [1, 2])
            ->selectRaw('MONTH(created_at) as mes, YEAR(created_at) as anio, count(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('anio', 'mes')
            ->orderBy('anio')->orderBy('mes')
            ->get();

        // Top profesionales por reseñas
        $topProfesionales = User::where('role_id', 2)
            ->withCount('resenas')
            ->orderByDesc('resenas_count')
            ->limit(5)
            ->get();

        // NUEVAS MÉTRICAS DE USUARIOS
        $nuevosEstaSemana = User::whereIn('role_id', [1, 2])->where('created_at', '>=', now()->startOfWeek())->count();
        $profesionalesActivos = \App\Models\Reserva::where('created_at', '>=', now()->startOfMonth())->distinct('profesional_id')->count('profesional_id');
        $clientesActivos = \App\Models\Reserva::where('created_at', '>=', now()->startOfMonth())->distinct('cliente_id')->count('cliente_id');
        $profesionalesVacaciones = \App\Models\PerfilProfesional::where('en_vacaciones', true)->count();

        // MÉTRICAS DE RESERVAS
        $totalReservas = \App\Models\Reserva::count();
        $reservasPendientes = \App\Models\Reserva::where('estado', 'pendiente')->count();
        $reservasConfirmadas = \App\Models\Reserva::where('estado', 'confirmada')->count();
        $reservasCompletadas = \App\Models\Reserva::where('estado', 'completada')->count();
        $reservasCanceladas = \App\Models\Reserva::where('estado', 'cancelada')->count();
        $tasaCancelacion = $totalReservas > 0 ? round(($reservasCanceladas / $totalReservas) * 100) : 0;
        $dineroRetenido = \App\Models\Reserva::where('estado_pago', 'retenido')->sum('monto');
        $dineroLiberado = \App\Models\Reserva::where('estado_pago', 'liberado')->sum('monto');
        $dineroReembolsado = \App\Models\Reserva::where('estado_pago', 'reembolsado')->sum('monto');

        // RESERVAS RECIENTES
        $reservasRecientes = \App\Models\Reserva::with(['cliente', 'profesional', 'servicio'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsuarios', 'totalClientes', 'totalProfesionales',
            'totalServicios', 'totalResenas', 'totalReportes',
            'serviciosPorCategoria', 'usuariosPorMes', 'topProfesionales',
            'nuevosEstaSemana', 'profesionalesActivos', 'clientesActivos', 'profesionalesVacaciones',
            'totalReservas', 'reservasPendientes', 'reservasConfirmadas',
            'reservasCompletadas', 'reservasCanceladas', 'tasaCancelacion',
            'dineroRetenido', 'dineroLiberado', 'dineroReembolsado',
            'reservasRecientes'
        ));
    }

    public function usuarios()
    {
        $usuarios = User::whereIn('role_id', [1, 2])->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.usuarios', compact('usuarios'));
    }

    public function eliminarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        if ($usuario->role_id === 3) {
            abort(403);
        }
        $usuario->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function servicios()
    {
        $servicios = Servicio::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.servicios', compact('servicios'));
    }

    public function eliminarServicio($id)
    {
        Servicio::findOrFail($id)->delete();

        return back()->with('success', 'Servicio eliminado correctamente.');
    }

    public function reportes(Request $request)
    {
        $tipo = $request->query('tipo');

        $totalResenas = \App\Models\Reporte::count();
        $totalServicios = \App\Models\ReporteServicio::where('tipo', 'servicio')->count();
        $totalProfesionales = \App\Models\ReporteServicio::where('tipo', 'profesional')->count();
        $totalDisputas = \App\Models\DisputaReserva::count();
        $totalTodos = $totalResenas + $totalServicios + $totalProfesionales + $totalDisputas;

        // Construir colección completa sin paginar primero
        $todos = collect();

        if (! $tipo || $tipo === 'resena') {
            \App\Models\Reporte::with(['resena.cliente', 'resena.profesional', 'user'])
                ->latest()->get()
                ->each(fn ($r) => $todos->push(['tipo' => 'resena', 'data' => $r, 'fecha' => $r->created_at]));
        }

        if (! $tipo || $tipo === 'servicio') {
            \App\Models\ReporteServicio::with(['servicio', 'profesional', 'user'])
                ->where('tipo', 'servicio')->latest()->get()
                ->each(fn ($r) => $todos->push(['tipo' => 'servicio', 'data' => $r, 'fecha' => $r->created_at]));
        }

        if (! $tipo || $tipo === 'profesional') {
            \App\Models\ReporteServicio::with(['profesional', 'user'])
                ->where('tipo', 'profesional')->latest()->get()
                ->each(fn ($r) => $todos->push(['tipo' => 'profesional', 'data' => $r, 'fecha' => $r->created_at]));
        }

        if (! $tipo || $tipo === 'disputa') {
            \App\Models\DisputaReserva::with(['reserva.servicio', 'cliente', 'profesional'])
                ->latest()->get()
                ->each(fn ($d) => $todos->push(['tipo' => 'disputa', 'data' => $d, 'fecha' => $d->created_at]));
        }

        $todos = $todos->sortByDesc('fecha')->values();

        $page = $request->get('page', 1);
        $perPage = 15;
        $reportes = new \Illuminate\Pagination\LengthAwarePaginator(
            $todos->forPage($page, $perPage),
            $todos->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.reportes', compact(
            'reportes', 'totalTodos', 'totalResenas', 'totalServicios', 'totalProfesionales', 'totalDisputas'
        ));
    }

    public function eliminarResena($resenaId)
    {
        Resena::findOrFail($resenaId)->delete();

        return back()->with('success', 'Reseña eliminada correctamente.');
    }

    public function ignorarReporte($id)
    {
        Reporte::findOrFail($id)->update(['estado' => 'revisado']);

        return back()->with('success', 'Reporte marcado como revisado.');
    }

    public function actualizarPerfil(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.auth()->id(),
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        auth()->user()->update($data);

        return back()->with('perfil_success', 'Perfil actualizado correctamente.');
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $usuarios = User::whereIn('role_id', [1, 2])
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            })->limit(5)->get()->map(fn ($u) => [
                'tipo' => 'usuario',
                'label' => $u->name,
                'sub' => $u->email,
                'badge' => $u->role_id === 1 ? 'Cliente' : 'Profesional',
                'url' => route('admin.usuarios') . '#item-usuario-' . $u->id,
                'id' => $u->id,
            ]);

        $servicios = \App\Models\Servicio::with('user')
            ->where('titulo', 'like', "%$q%")
            ->limit(5)->get()->map(fn ($s) => [
                'tipo' => 'servicio',
                'label' => $s->titulo,
                'sub' => $s->user->name ?? '—',
                'badge' => ucfirst($s->categoria),
                'url' => route('admin.servicios') . '#item-servicio-' . $s->id,
                'id' => $s->id,
            ]);

        $reportes = \App\Models\Reporte::with(['user', 'resena'])
            ->where('motivo', 'like', "%$q%")
            ->limit(3)->get()->map(fn ($r) => [
                'tipo' => 'reporte',
                'label' => 'Reporte: '.$r->motivo,
                'sub' => 'Por '.($r->user->name ?? '—'),
                'badge' => 'Reporte',
                'url' => route('admin.reportes') . '#item-reporte-' . $r->id,
                'id' => $r->id,
            ]);

        return response()->json([
            'resultados' => $usuarios->concat($servicios)->concat($reportes)->values(),
        ]);
    }

    public function crearAdmin()
    {
        return view('admin.crear-admin');
    }

    public function guardarAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => bcrypt($request->password),
            'role_id' => 3,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Administrador creado correctamente.');
    }

    public function verUsuario($id)
    {
        $usuario = User::with([
            'servicios',
            'perfilProfesional.fotos',
            'perfilProfesional.negocio',
            'resenas',
        ])->findOrFail($id);

        $reservasCliente = \App\Models\Reserva::where('cliente_id', $id)
            ->with('servicio')
            ->latest()
            ->take(5)
            ->get();

        $reservasProfesional = \App\Models\Reserva::where('profesional_id', $id)
            ->with(['servicio', 'cliente'])
            ->latest()
            ->take(5)
            ->get();

        $disputasCliente = \App\Models\DisputaReserva::where('cliente_id', $id)->count();
        $disputasProfesional = \App\Models\DisputaReserva::where('profesional_id', $id)->count();

        return response()->json([
            'usuario' => $usuario,
            'reservasCliente' => $reservasCliente,
            'reservasProfesional' => $reservasProfesional,
            'disputasCliente' => $disputasCliente,
            'disputasProfesional' => $disputasProfesional,
        ]);
    }

    public function eliminarServicioReporte($servicioId)
    {
        $servicio = \App\Models\Servicio::findOrFail($servicioId);
        // Marcar reportes como eliminados
        \App\Models\ReporteServicio::where('servicio_id', $servicioId)
            ->update(['estado' => 'eliminado']);
        $servicio->delete();

        return back()->with('success', 'Servicio eliminado correctamente.');
    }

    public function ignorarReporteServicio($id)
    {
        \App\Models\ReporteServicio::findOrFail($id)->update(['estado' => 'revisado']);

        return back()->with('success', 'Reporte marcado como revisado.');
    }

    public function resolverDisputaProfesional(Request $request, $id)
    {
        $disputa = \App\Models\DisputaReserva::findOrFail($id);
        $reserva = $disputa->reserva;

        $disputa->update([
            'estado' => 'resuelto_profesional',
            'resolucion_admin' => $request->resolucion_admin ?? '',
            'resuelto_at' => now()
        ]);

        $reserva->update([
            'estado_pago' => 'liberado',
            'liberado_at' => now(),
        ]);

        return back()->with('success', 'Disputa resuelta a favor del profesional. El pago ha sido liberado.');
    }

    public function resolverDisputaCliente(Request $request, $id)
    {
        $disputa = \App\Models\DisputaReserva::findOrFail($id);
        $reserva = $disputa->reserva;

        $disputa->update([
            'estado' => 'resuelto_cliente',
            'resolucion_admin' => $request->resolucion_admin ?? '',
            'resuelto_at' => now()
        ]);

        $reserva->update([
            'estado_pago' => 'reembolsado',
            'estado' => 'cancelada'
        ]);

        return back()->with('success', 'Disputa resuelta a favor del cliente. El pago será reembolsado.');
    }

    public function deshabilitarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        if ($usuario->role_id === 3) {
            abort(403);
        }
        
        $usuario->update([
            'activo' => !$usuario->activo
        ]);

        $estado = $usuario->activo ? 'habilitado' : 'deshabilitado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }
}

{{-- OVERLAY DRAWER USUARIO --}}
<div id="usuarioOverlay"
    onclick="cerrarUsuario()"
    class="fixed inset-0 bg-gray-900/45 z-[100] hidden cursor-pointer">
</div>

{{-- DRAWER DETALLE USUARIO --}}
<aside id="usuarioDrawer"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-[480px] z-[200] flex flex-col sm:rounded-l-3xl overflow-hidden shadow-2xl"
    style="transform: translateX(100%); transition: transform 0.55s cubic-bezier(.77,0,.18,1);">

    <div id="usuarioHeader" class="relative bg-blue-600 px-6 sm:px-9 pt-8 pb-7 flex-shrink-0 overflow-hidden">
        <div style="position:absolute;width:280px;height:280px;border-radius:50%;border:40px solid rgba(255,255,255,0.08);top:-80px;right:-80px;pointer-events:none;"></div>
        <button onclick="cerrarUsuario()"
            class="absolute top-4 right-5 w-8 h-8 rounded-full bg-white/15 border border-white/25 flex items-center justify-center text-white text-sm hover:bg-white/25 transition z-10 cursor-pointer">
            ✕
        </button>
        <div class="relative z-10 flex items-center gap-3 mb-5">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 text-base">K</div>
            <span class="font-extrabold text-white text-base">Kliksy Admin</span>
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div id="usuarioAvatar"
                class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
            </div>
            <div class="min-w-0">
                <h2 id="usuarioNombre" class="text-xl text-white font-bold leading-tight truncate"></h2>
                <p id="usuarioEmail" class="text-sm text-white/70 truncate"></p>
                <span id="usuarioRolBadge" class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-full mt-1 inline-block"></span>
            </div>
        </div>
    </div>

    <div class="bg-white flex-1 overflow-y-auto px-6 sm:px-9 pt-7 pb-8" id="usuarioBody">
        <div class="flex items-center justify-center py-16 text-gray-400">
            <i class="bi bi-arrow-clockwise text-3xl"></i>
        </div>
    </div>
</aside>

@push('scripts')
<script>
function verUsuario(id) {
    document.getElementById('usuarioDrawer').style.transform = 'translateX(0)';
    document.getElementById('usuarioOverlay').classList.remove('hidden');
    document.getElementById('usuarioBody').innerHTML = `
        <div class="flex items-center justify-center py-16 text-gray-400">
            <i class="bi bi-arrow-clockwise text-3xl animate-spin"></i>
        </div>`;

    fetch(`{{ url('admin/usuarios') }}/${id}/detalle`)
        .then(r => r.json())
        .then(data => {
            const u = data.usuario;
            document.getElementById('usuarioAvatar').textContent = u.name.substring(0,2).toUpperCase();
            document.getElementById('usuarioNombre').textContent = u.name;
            document.getElementById('usuarioEmail').textContent = u.email;
            document.getElementById('usuarioRolBadge').textContent = u.role_id === 1 ? 'Cliente' : 'Profesional';

            let html = `
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Información personal</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="bi bi-person text-gray-400 w-4 flex-shrink-0"></i>
                            <span class="text-gray-700">${u.name}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <i class="bi bi-envelope text-gray-400 w-4 flex-shrink-0"></i>
                            <span class="text-gray-700 break-all">${u.email}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <i class="bi bi-telephone text-gray-400 w-4 flex-shrink-0"></i>
                            <span class="text-gray-700">${u.telefono ?? 'Sin teléfono'}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <i class="bi bi-calendar text-gray-400 w-4 flex-shrink-0"></i>
                            <span class="text-gray-700">Registrado el ${new Date(u.created_at).toLocaleDateString('es-CO')}</span>
                        </div>
                    </div>
                </div>`;

            if (data.disputasCliente > 0 || data.disputasProfesional > 0) {
                html += `
                <div class="mb-6 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-red-500 mb-3 flex items-center gap-1.5"><i class="bi bi-exclamation-triangle"></i> Historial de Disputas</p>
                    <div class="bg-red-50 rounded-xl p-3 border border-red-100 flex flex-col gap-1.5">
                        ${data.disputasCliente > 0 ? `<p class="text-xs text-red-800"><b>Como Cliente:</b> ${data.disputasCliente} reportes generados. Revise posibles estafas recurrentes.</p>` : ''}
                        ${data.disputasProfesional > 0 ? `<p class="text-xs text-red-800"><b>Como Profesional:</b> ${data.disputasProfesional} servicios disputados.</p>` : ''}
                    </div>
                </div>`;
            }

            if (u.role_id === 2) {
                html += `
                <div class="mb-6 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Servicios (${u.servicios?.length ?? 0})</p>`;
                if (u.servicios?.length > 0) {
                    u.servicios.forEach(s => {
                        html += `
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <div class="min-w-0 mr-2">
                                <p class="text-sm font-medium text-gray-800 truncate">${s.titulo}</p>
                                <p class="text-xs text-gray-400">${s.categoria}</p>
                            </div>
                            <span class="text-blue-600 font-semibold text-sm flex-shrink-0">$${parseFloat(s.precio).toLocaleString('es-CO')}</span>
                        </div>`;
                    });
                } else {
                    html += `<p class="text-sm text-gray-400">Sin servicios registrados.</p>`;
                }
                html += `</div>`;

                html += `
                <div class="mb-6 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Reservas recibidas (${data.reservasProfesional?.length ?? 0})</p>`;
                if (data.reservasProfesional?.length > 0) {
                    data.reservasProfesional.slice(0,5).forEach(s => {
                        const estados = { pendiente:'bg-yellow-50 text-yellow-600', confirmada:'bg-blue-50 text-blue-600', completada:'bg-green-50 text-green-600', cancelada:'bg-red-50 text-red-500' };
                        const fec = new Date(s.fecha + 'T' + s.hora_inicio);
                        let fechaFormateada = '';
                        if(!isNaN(fec)){
                            fechaFormateada = fec.toLocaleString('es-CO', {day: '2-digit', month: 'short', hour:'numeric', minute:'2-digit'});
                        } else {
                            fechaFormateada = s.fecha + ' ' + s.hora_inicio;
                        }
                        
                        html += `
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <div class="min-w-0 mr-2 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">${s.servicio?.titulo ?? '—'}</p>
                                <p class="text-[10px] sm:text-xs text-gray-400 break-words line-clamp-1"><i class="bi bi-person"></i> ${s.cliente?.name ?? '—'} &bull; <i class="bi bi-calendar"></i> ${fechaFormateada}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full flex-shrink-0 ${estados[s.estado] ?? 'bg-gray-100 text-gray-500'} mb-1">${s.estado}</span>
                                <span class="text-xs text-gray-500 font-semibold">$${parseFloat(s.monto).toLocaleString('es-CO')}</span>
                            </div>
                        </div>`;
                    });
                } else {
                    html += `<p class="text-sm text-gray-400">Sin reservas.</p>`;
                }
                html += `</div>`;
            }

            if (u.role_id === 1) {
                html += `
                <div class="mb-6 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Reservas realizadas (${data.reservasCliente?.length ?? 0})</p>`;
                if (data.reservasCliente?.length > 0) {
                    data.reservasCliente.slice(0,5).forEach(s => {
                        const estados = { pendiente:'bg-yellow-50 text-yellow-600', confirmada:'bg-blue-50 text-blue-600', completada:'bg-green-50 text-green-600', cancelada:'bg-red-50 text-red-500' };
                        const fec = new Date(s.fecha + 'T' + s.hora_inicio);
                        let fechaFormateada = '';
                        if(!isNaN(fec)){
                            fechaFormateada = fec.toLocaleString('es-CO', {day: '2-digit', month: 'short', hour:'numeric', minute:'2-digit'});
                        } else {
                            fechaFormateada = s.fecha + ' ' + s.hora_inicio;
                        }
                        
                        html += `
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <div class="min-w-0 mr-2 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">${s.servicio?.titulo ?? '—'}</p>
                                <p class="text-xs text-gray-400 break-words line-clamp-1"><i class="bi bi-calendar"></i> ${fechaFormateada}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full flex-shrink-0 ${estados[s.estado] ?? 'bg-gray-100 text-gray-500'} mb-1">${s.estado}</span>
                                <span class="text-xs text-gray-500 font-semibold">$${parseFloat(s.monto).toLocaleString('es-CO')}</span>
                            </div>
                        </div>`;
                    });
                } else {
                    html += `<p class="text-sm text-gray-400">Sin reservas.</p>`;
                }
                html += `</div>`;
            }

            document.getElementById('usuarioBody').innerHTML = html;
        });
}

function cerrarUsuario() {
    document.getElementById('usuarioDrawer').style.transform = 'translateX(100%)';
    document.getElementById('usuarioOverlay').classList.add('hidden');
}
</script>
@endpush

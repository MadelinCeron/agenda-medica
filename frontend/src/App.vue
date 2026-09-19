<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import esLocale from '@fullcalendar/core/locales/es'
import { actualizarCita, cambiarEstadoCita, crearCita, obtenerCitas, obtenerDoctores, obtenerPacientes } from './services/api'

const estados = [
  { valor: 'pendiente', etiqueta: 'Pendiente' },
  { valor: 'confirmada', etiqueta: 'Confirmada' },
  { valor: 'cancelada', etiqueta: 'Cancelada' },
  { valor: 'atendida', etiqueta: 'Atendida' },
]
const doctores = ref([])
const pacientes = ref([])
const filtroDoctor = ref('')
const cargando = ref(true)
const mensaje = ref('')
const error = ref('')
const modalAbierto = ref(false)
const modoModal = ref('crear')
const citaSeleccionada = ref(null)
const formulario = reactive({ paciente_id: '', doctor_id: '', fecha: '', hora_inicio: '09:00', hora_fin: '09:30', motivo: '', estado: 'pendiente' })

const nombreDoctor = computed(() => nombreCompleto(doctores.value.find((item) => item.id === citaSeleccionada.value?.doctor_id)))
const nombrePaciente = computed(() => nombreCompleto(pacientes.value.find((item) => item.id === citaSeleccionada.value?.paciente_id)))
const especialidadDoctor = computed(() => doctores.value.find((item) => item.id === citaSeleccionada.value?.doctor_id)?.especialidad || 'No indicada')
const tituloModal = computed(() => modoModal.value === 'ver' ? 'Detalle de la cita' : modoModal.value === 'editar' ? 'Editar cita' : 'Nueva cita')
const opcionesCalendario = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin], locale: esLocale, initialView: 'dayGridMonth',
  headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
  buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana' }, editable: true, selectable: true, dayMaxEvents: 3, height: 'auto',
  events: [], dateClick: abrirNuevaCita, eventClick: abrirDetalle, eventDrop: guardarReprogramacion,
})

function nombreCompleto(persona) { return persona ? `${persona.nombres} ${persona.apellidos}` : 'Sin asignar' }
function colorEstado(estado) { return { pendiente: '#bd7b22', confirmada: '#287a68', cancelada: '#9b4d58', atendida: '#476a9b' }[estado] || '#476a9b' }
function textoError(exception, fallback = 'No se pudo completar la operación.') {
  const respuesta = exception.response?.data
  return respuesta?.message || (respuesta?.errors ? Object.values(respuesta.errors).flat()[0] : fallback)
}
function fechaLocal(date) {
  const pad = (value) => String(value).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}:00`
}
function fechaVisible(value) { return new Intl.DateTimeFormat('es-ES', { dateStyle: 'full', timeStyle: 'short' }).format(new Date(value)) }
function mapearEventos(data) {
  return data.map((cita) => ({ id: String(cita.id), title: `${nombreCompleto(cita.paciente)} · ${cita.motivo}`, start: cita.fecha_inicio, end: cita.fecha_fin, backgroundColor: colorEstado(cita.estado), borderColor: colorEstado(cita.estado), extendedProps: { cita } }))
}
async function cargarDatos() {
  cargando.value = true; error.value = ''
  try {
    const filtros = filtroDoctor.value ? { doctor_id: filtroDoctor.value } : {}
    const [citasResponse, doctoresResponse, pacientesResponse] = await Promise.all([obtenerCitas(filtros), obtenerDoctores(), obtenerPacientes()])
    doctores.value = doctoresResponse.data.data; pacientes.value = pacientesResponse.data.data
    opcionesCalendario.events = mapearEventos(citasResponse.data.data)
  } catch (exception) { error.value = textoError(exception, 'No se pudieron cargar los datos de la agenda.') } finally { cargando.value = false }
}
function limpiarFormulario() {
  formulario.paciente_id = pacientes.value[0]?.id || ''; formulario.doctor_id = doctores.value[0]?.id || ''; formulario.fecha = new Date().toISOString().slice(0, 10)
  formulario.hora_inicio = '09:00'; formulario.hora_fin = '09:30'; formulario.motivo = ''; formulario.estado = 'pendiente'
}
function abrirNuevaCita(info = null) { limpiarFormulario(); if (info?.dateStr) formulario.fecha = info.dateStr.slice(0, 10); citaSeleccionada.value = null; modoModal.value = 'crear'; modalAbierto.value = true }
function abrirDetalle(info) { citaSeleccionada.value = info.event.extendedProps.cita; modoModal.value = 'ver'; modalAbierto.value = true }
function editarCita() {
  const cita = citaSeleccionada.value
  Object.assign(formulario, { paciente_id: cita.paciente_id, doctor_id: cita.doctor_id, fecha: cita.fecha_inicio.slice(0, 10), hora_inicio: cita.fecha_inicio.slice(11, 16), hora_fin: cita.fecha_fin.slice(11, 16), motivo: cita.motivo, estado: cita.estado })
  modoModal.value = 'editar'
}
function cerrarModal() { modalAbierto.value = false }
async function guardarCita() {
  error.value = ''; const datos = { paciente_id: Number(formulario.paciente_id), doctor_id: Number(formulario.doctor_id), fecha_inicio: `${formulario.fecha}T${formulario.hora_inicio}:00`, fecha_fin: `${formulario.fecha}T${formulario.hora_fin}:00`, motivo: formulario.motivo, estado: formulario.estado }
  try { if (modoModal.value === 'editar') { await actualizarCita(citaSeleccionada.value.id, datos); mensaje.value = 'La cita se actualizó correctamente.' } else { await crearCita(datos); mensaje.value = 'La cita se creó correctamente.' }; cerrarModal(); await cargarDatos() } catch (exception) { error.value = textoError(exception) }
}
async function cambiarEstado() {
  error.value = ''
  try { const response = await cambiarEstadoCita(citaSeleccionada.value.id, citaSeleccionada.value.estado); citaSeleccionada.value = response.data.data; mensaje.value = 'El estado de la cita se actualizó correctamente.'; await cargarDatos() } catch (exception) { error.value = textoError(exception) }
}
async function guardarReprogramacion(info) {
  const cita = info.event.extendedProps.cita; const inicio = info.event.start; const fin = info.event.end || new Date(inicio.getTime() + 30 * 60 * 1000)
  try { await actualizarCita(cita.id, { fecha_inicio: fechaLocal(inicio), fecha_fin: fechaLocal(fin) }); mensaje.value = 'La cita se reprogramó correctamente.'; await cargarDatos() } catch (exception) { info.revert(); error.value = textoError(exception, 'No se pudo reprogramar la cita.') }
}
onMounted(cargarDatos)
</script>

<template>
  <main class="pagina">
    <header class="encabezado"><div><p class="ceja">Gestión de consultas</p><h1>Agenda Médica</h1><p class="subtitulo">Organiza la atención de tu clínica con una agenda clara y actualizada.</p></div><button class="boton boton-principal" type="button" @click="abrirNuevaCita()">+ Nueva cita</button></header>
    <section class="barra-herramientas" aria-label="Filtros de agenda"><label class="campo-filtro"><span>Vista por doctor</span><select v-model="filtroDoctor" @change="cargarDatos"><option value="">Todos los doctores</option><option v-for="doctor in doctores" :key="doctor.id" :value="doctor.id">{{ nombreCompleto(doctor) }}</option></select></label><div class="leyenda"><span v-for="estado in estados" :key="estado.valor" class="leyenda-item"><i :class="['punto-estado', `estado-${estado.valor}`]" />{{ estado.etiqueta }}</span></div></section>
    <p v-if="mensaje" class="alerta alerta-exito" role="status">{{ mensaje }}</p><p v-if="error" class="alerta alerta-error" role="alert">{{ error }}</p>
    <section class="calendario-panel"><div v-if="cargando" class="cargando">Cargando agenda...</div><FullCalendar v-else :options="opcionesCalendario" /></section>
    <div v-if="modalAbierto" class="modal-fondo" @click.self="cerrarModal"><section class="modal" role="dialog" aria-modal="true" :aria-label="tituloModal">
      <div class="modal-cabecera"><div><p class="ceja">Agenda</p><h2>{{ tituloModal }}</h2></div><button class="boton-cerrar" type="button" aria-label="Cerrar" @click="cerrarModal">×</button></div>
      <div v-if="modoModal === 'ver'" class="detalle-cita"><div class="detalle-principal"><span :class="['etiqueta-estado', `estado-${citaSeleccionada.estado}`]">{{ estados.find((item) => item.valor === citaSeleccionada.estado)?.etiqueta }}</span><h3>{{ citaSeleccionada.motivo }}</h3><p>{{ fechaVisible(citaSeleccionada.fecha_inicio) }}</p></div><dl class="datos-cita"><div><dt>Paciente</dt><dd>{{ nombrePaciente }}</dd></div><div><dt>Doctor</dt><dd>{{ nombreDoctor }}</dd></div><div><dt>Especialidad</dt><dd>{{ especialidadDoctor }}</dd></div><div><dt>Horario</dt><dd>{{ citaSeleccionada.fecha_inicio.slice(11, 16) }} a {{ citaSeleccionada.fecha_fin.slice(11, 16) }}</dd></div></dl><label class="campo-formulario"><span>Estado</span><select v-model="citaSeleccionada.estado" @change="cambiarEstado"><option v-for="estado in estados" :key="estado.valor" :value="estado.valor">{{ estado.etiqueta }}</option></select></label><div class="modal-acciones"><button class="boton boton-secundario" type="button" @click="editarCita">Editar cita</button></div></div>
      <form v-else class="formulario" @submit.prevent="guardarCita"><div class="form-grid"><label class="campo-formulario"><span>Paciente</span><select v-model="formulario.paciente_id" required><option v-for="paciente in pacientes" :key="paciente.id" :value="paciente.id">{{ nombreCompleto(paciente) }}</option></select></label><label class="campo-formulario"><span>Doctor</span><select v-model="formulario.doctor_id" required><option v-for="doctor in doctores" :key="doctor.id" :value="doctor.id">{{ nombreCompleto(doctor) }} · {{ doctor.especialidad }}</option></select></label><label class="campo-formulario"><span>Fecha</span><input v-model="formulario.fecha" type="date" required /></label><label class="campo-formulario"><span>Hora de inicio</span><input v-model="formulario.hora_inicio" type="time" required /></label><label class="campo-formulario"><span>Hora de fin</span><input v-model="formulario.hora_fin" type="time" required /></label><label class="campo-formulario campo-ancho"><span>Motivo</span><input v-model="formulario.motivo" type="text" maxlength="255" placeholder="Motivo de la consulta" required /></label></div><div class="modal-acciones"><button class="boton boton-secundario" type="button" @click="cerrarModal">Cancelar</button><button class="boton boton-principal" type="submit">Guardar cita</button></div></form>
    </section></div>
  </main>
</template>

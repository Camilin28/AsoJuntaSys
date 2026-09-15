<?php
require_once '../includes/auth.php';
require '../config/db.php';

requireLogin();
$csrfToken = generarTokenCSRF();

$dashboardsPorRol = [
    'Presidente General' => 'dashboard_presidente.php',
    'Presidentes de JAC' => 'dashboard_jac.php',
    'Secretaría'         => 'dashboard_secretario.php',
    'Tesorería'          => 'dashboard_tesoreria.php',
];
$urlDashboard = $dashboardsPorRol[$_SESSION['usuario_rol']] ?? 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asistente Virtual - AsoJuntaSys</title>
<style>
  body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 24px; }
  .contenedor { max-width: 700px; margin: 0 auto; }
  .volver { display: inline-block; margin-bottom: 16px; color: #2E7D32; text-decoration: none; font-weight: bold; }
  h2 { color: #2E7D32; margin-bottom: 4px; }
  .subtitulo { color: #666; font-size: 13px; margin-bottom: 20px; }
  .chat-box {
    background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    height: 420px; overflow-y: auto; padding: 16px; margin-bottom: 14px;
  }
  .msg { max-width: 80%; margin-bottom: 12px; padding: 10px 14px; border-radius: 10px; font-size: 14px; line-height: 1.4; white-space: pre-wrap; }
  .msg.usuario { background: #2E7D32; color: #fff; margin-left: auto; border-bottom-right-radius: 2px; }
  .msg.asistente { background: #eef3ee; color: #222; margin-right: auto; border-bottom-left-radius: 2px; }
  .msg.cargando { background: #eef3ee; color: #888; font-style: italic; }
  .form-chat { display: flex; gap: 8px; }
  .form-chat input[type=text] {
    flex: 1; padding: 12px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 14px;
  }
  .form-chat button {
    padding: 12px 20px; background: #2E7D32; color: #fff; border: none; border-radius: 8px;
    font-weight: bold; cursor: pointer;
  }
  .form-chat button:disabled { background: #a5c9a8; cursor: default; }
  .sugerencias { margin-top: 10px; font-size: 12px; color: #777; }
  .sugerencias span { display: inline-block; background: #eef3ee; padding: 4px 10px; border-radius: 12px; margin: 3px 4px 0 0; cursor: pointer; }
</style>
</head>
<body>
<div class="contenedor">
  <a class="volver" href="<?= htmlspecialchars($urlDashboard) ?>">← Volver</a>
  <h2>Asistente Virtual</h2>
  <div class="subtitulo">Pregunta en lenguaje natural sobre actas, documentos, agenda<?= in_array($_SESSION['usuario_rol'], ['Tesorería','Presidente General']) ? ' y finanzas' : '' ?> de tu JAC. <span id="btnReiniciar" style="cursor:pointer; color:#2E7D32; text-decoration:underline;">Nueva conversación</span></div>

  <div class="chat-box" id="chatBox">
    <div class="msg asistente" id="mensajeBienvenida">Hola, soy el asistente virtual de AsoJuntaSys. Puedo ayudarte a consultar información de actas, documentos, agenda<?= in_array($_SESSION['usuario_rol'], ['Tesorería','Presidente General']) ? ' y el resumen financiero' : '' ?>. ¿En qué te ayudo?</div>
  </div>

  <div class="sugerencias">
    Prueba con:
    <span onclick="preguntarSugerida(this)">¿Cuáles fueron los acuerdos de la última reunión?</span>
    <span onclick="preguntarSugerida(this)">¿Qué eventos hay próximamente?</span>
    <span onclick="preguntarSugerida(this)">¿Cuántos documentos están pendientes?</span>
  </div>

  <form class="form-chat" id="formChat" style="margin-top:12px;">
    <input type="text" id="pregunta" placeholder="Escribe tu pregunta..." maxlength="500" autocomplete="off" required>
    <button type="submit" id="btnEnviar">Enviar</button>
  </form>
</div>

<script>
const csrfToken = <?= json_encode($csrfToken) ?>;
const chatBox = document.getElementById('chatBox');
const form = document.getElementById('formChat');
const input = document.getElementById('pregunta');
const btn = document.getElementById('btnEnviar');
const btnReiniciar = document.getElementById('btnReiniciar');

btnReiniciar.addEventListener('click', async function () {
  try {
    await fetch('../controllers/reiniciar_chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'csrf_token=' + encodeURIComponent(csrfToken)
    });
  } catch (err) {}
  const bienvenida = document.getElementById('mensajeBienvenida');
  chatBox.innerHTML = '';
  chatBox.appendChild(bienvenida);
});

function agregarMensaje(texto, clase) {
  const div = document.createElement('div');
  div.className = 'msg ' + clase;
  div.textContent = texto;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
  return div;
}

function preguntarSugerida(el) {
  input.value = el.textContent;
  form.requestSubmit();
}

form.addEventListener('submit', async function (e) {
  e.preventDefault();
  const pregunta = input.value.trim();
  if (!pregunta) return;

  agregarMensaje(pregunta, 'usuario');
  input.value = '';
  btn.disabled = true;
  const cargando = agregarMensaje('Pensando...', 'cargando');

  try {
    const resp = await fetch('../controllers/consultar_asistente.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'pregunta=' + encodeURIComponent(pregunta) + '&csrf_token=' + encodeURIComponent(csrfToken)
    });
    const data = await resp.json();
    cargando.remove();
    agregarMensaje(data.respuesta, 'asistente');
  } catch (err) {
    cargando.remove();
    agregarMensaje('No se pudo conectar con el asistente. Intenta de nuevo.', 'asistente');
  } finally {
    btn.disabled = false;
    input.focus();
  }
});
</script>
</body>
</html>
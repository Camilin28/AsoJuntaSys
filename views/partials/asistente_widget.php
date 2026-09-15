<?php
// Requiere que la página que incluye este parcial ya haya definido $csrfToken
// (llamando a generarTokenCSRF() después de require_once auth.php).
$mostrarFinanciero = in_array($_SESSION['usuario_rol'] ?? '', ['Tesorería', 'Presidente General']);
?>
<div id="asistenteBurbuja" style="position:fixed; bottom:24px; right:24px; z-index:9999;">
  <button id="asistenteToggle" title="Asistente Virtual" style="width:60px; height:60px; border-radius:50%; background:#2E7D32; color:#fff; border:none; box-shadow:0 4px 14px rgba(0,0,0,0.28); cursor:pointer; font-size:26px; display:flex; align-items:center; justify-content:center;">
    🤖
  </button>
</div>

<div id="asistentePanel" style="display:none; flex-direction:column; position:fixed; bottom:96px; right:24px; width:360px; max-width:92vw; height:480px; max-height:75vh; background:#fff; border-radius:14px; box-shadow:0 10px 34px rgba(0,0,0,0.28); z-index:9999; overflow:hidden; font-family: Arial, sans-serif;">

  <div style="background:#2E7D32; color:#fff; padding:14px 16px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
    <div>
      <strong style="font-size:14px;">Asistente Virtual</strong><br>
      <span style="font-size:11px; opacity:.85;">AsoJuntaSys</span>
    </div>
    <span id="asistenteCerrar" style="cursor:pointer; font-size:22px; line-height:1;">&times;</span>
  </div>

  <div style="padding:6px 10px; border-bottom:1px solid #eee; text-align:right; flex-shrink:0;">
    <span id="asistenteReiniciar" style="cursor:pointer; font-size:11px; color:#2E7D32; text-decoration:underline;">Nueva conversación</span>
  </div>

  <div id="chatBoxFlot" style="flex:1; overflow-y:auto; padding:14px; background:#f7f9f7;">
    <div class="msg-flot asistente" id="mensajeBienvenidaFlot">Hola, soy el asistente virtual de AsoJuntaSys. Puedo ayudarte a consultar actas, documentos, agenda<?= $mostrarFinanciero ? ' y finanzas' : '' ?>. ¿En qué te ayudo?</div>
  </div>

  <form id="formChatFlot" style="display:flex; gap:6px; padding:10px; border-top:1px solid #eee; flex-shrink:0;">
    <input type="text" id="preguntaFlot" placeholder="Escribe tu pregunta..." maxlength="500" autocomplete="off" required
           style="flex:1; padding:9px 11px; border:1px solid #ccc; border-radius:8px; font-size:13px;">
    <button type="submit" id="btnEnviarFlot" style="background:#2E7D32; color:#fff; border:none; border-radius:8px; padding:0 16px; font-weight:bold; cursor:pointer; font-size:16px;">➤</button>
  </form>
</div>

<style>
  .msg-flot { max-width:85%; margin-bottom:10px; padding:9px 12px; border-radius:10px; font-size:13px; line-height:1.4; white-space:pre-wrap; }
  .msg-flot.usuario { background:#2E7D32; color:#fff; margin-left:auto; border-bottom-right-radius:2px; }
  .msg-flot.asistente { background:#eef3ee; color:#222; margin-right:auto; border-bottom-left-radius:2px; }
  .msg-flot.cargando { background:#eef3ee; color:#888; font-style:italic; }
</style>

<script>
(function () {
  const csrfTokenFlot = <?= json_encode($csrfToken) ?>;
  const burbuja = document.getElementById('asistenteBurbuja');
  const panel = document.getElementById('asistentePanel');
  const toggle = document.getElementById('asistenteToggle');
  const cerrar = document.getElementById('asistenteCerrar');
  const reiniciar = document.getElementById('asistenteReiniciar');
  const chatBox = document.getElementById('chatBoxFlot');
  const form = document.getElementById('formChatFlot');
  const input = document.getElementById('preguntaFlot');
  const btn = document.getElementById('btnEnviarFlot');

  toggle.addEventListener('click', function () {
    const abierto = panel.style.display === 'flex';
    panel.style.display = abierto ? 'none' : 'flex';
    if (!abierto) input.focus();
  });
  cerrar.addEventListener('click', function () {
    panel.style.display = 'none';
  });

  reiniciar.addEventListener('click', async function () {
    try {
      await fetch('../controllers/reiniciar_chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'csrf_token=' + encodeURIComponent(csrfTokenFlot)
      });
    } catch (err) {}
    const bienvenida = document.getElementById('mensajeBienvenidaFlot');
    chatBox.innerHTML = '';
    chatBox.appendChild(bienvenida);
  });

  function agregarMensaje(texto, clase) {
    const div = document.createElement('div');
    div.className = 'msg-flot ' + clase;
    div.textContent = texto;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
    return div;
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
        body: 'pregunta=' + encodeURIComponent(pregunta) + '&csrf_token=' + encodeURIComponent(csrfTokenFlot)
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
})();
</script>
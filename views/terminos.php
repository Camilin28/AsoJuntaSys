<?php
// Datos editables de la página (cámbialos aquí, no en el HTML)
$version             = '2.0';
$fecha_actualizacion = '6 de octubre de 2026';
$correo_contacto     = 'contacto@tudominio.com'; // <- reemplazar por el correo real de la JAC / administrador
$nombre_responsable  = 'Junta de Acción Comunal del barrio Bella Vista, Arbeláez (Cundinamarca)';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Términos y Condiciones - AsoJuntaSys</title>
    <link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
    <style>
        :root{
            --verde:#2e7d32;
            --verde-oscuro:#1b5e20;
            --texto:#1f2a21;
            --suave:#5b665d;
            --linea:#d9e2da;
            --fondo:#f7f9f7;
        }
        *{ box-sizing:border-box; }
        body{
            margin:0;
            background:var(--fondo);
            color:var(--texto);
            font-family:Georgia, "Times New Roman", serif;
            font-size:1.05rem;
            line-height:1.75;
        }
        .pagina{
            max-width:46rem;
            margin:0 auto;
            padding:2.5rem 1.25rem 4rem;
        }
        h1,h2,h3,nav,.meta,.volver{
            font-family:"Segoe UI", Arial, sans-serif;
        }
        h1{
            color:var(--verde-oscuro);
            font-size:1.9rem;
            line-height:1.25;
            margin:0 0 .25rem;
        }
        .subtitulo{
            font-family:"Segoe UI", Arial, sans-serif;
            color:var(--suave);
            margin:0 0 1rem;
            font-size:1.1rem;
        }
        .meta{
            font-size:.9rem;
            color:var(--suave);
            border-top:1px solid var(--linea);
            border-bottom:1px solid var(--linea);
            padding:.6rem 0;
            margin-bottom:1.5rem;
        }
        nav{
            border-left:4px solid var(--verde);
            background:#fff;
            padding:.9rem 1.2rem;
            margin:0 0 2rem;
            font-size:.95rem;
        }
        nav strong{ display:block; margin-bottom:.4rem; }
        nav ol{ margin:0; padding-left:1.2rem; columns:2; column-gap:2rem; }
        nav li{ margin:.15rem 0; break-inside:avoid; }
        nav a{ color:var(--verde-oscuro); text-decoration:none; }
        nav a:hover{ text-decoration:underline; }
        h2{
            color:var(--verde-oscuro);
            font-size:1.25rem;
            margin:2.2rem 0 .5rem;
            scroll-margin-top:1rem;
        }
        h3{
            font-size:1.02rem;
            margin:1.4rem 0 .2rem;
            color:var(--texto);
        }
        p{ margin:.5rem 0 .9rem; }
        ul{ margin:.3rem 0 1rem; padding-left:1.4rem; }
        li{ margin:.25rem 0; }
        .aviso{
            background:#fff;
            border:1px solid var(--linea);
            border-radius:6px;
            padding:.8rem 1rem;
            font-size:.97rem;
        }
        .volver{
            display:inline-block;
            margin-top:2.5rem;
            color:var(--verde-oscuro);
            font-weight:600;
            text-decoration:none;
        }
        .volver:hover{ text-decoration:underline; }
        a:focus-visible{ outline:3px solid var(--verde); outline-offset:2px; }
        @media (max-width:600px){
            nav ol{ columns:1; }
            h1{ font-size:1.55rem; }
        }
        @media print{
            body{ background:#fff; font-size:11pt; }
            nav,.volver{ display:none; }
        }
    </style>
</head>
<body>
<main class="pagina">

    <h1>Términos y Condiciones de Uso</h1>
    <p class="subtitulo">Portal web AsoJuntaSys · Política de tratamiento de datos personales</p>
    <p class="meta">Versión <?= htmlspecialchars($version) ?> · Última actualización: <?= htmlspecialchars($fecha_actualizacion) ?></p>

    <nav aria-label="Contenido">
        <strong>Contenido</strong>
        <ol>
            <li><a href="#objeto">Objeto y partes</a></li>
            <li><a href="#aceptacion">Aceptación de los términos</a></li>
            <li><a href="#finalidad">Finalidad del sistema</a></li>
            <li><a href="#usuarios">Usuarios y cuentas</a></li>
            <li><a href="#responsabilidades">Responsabilidades del usuario</a></li>
            <li><a href="#datos">Tratamiento de datos personales</a></li>
            <li><a href="#auditoria">Registro de actividad</a></li>
            <li><a href="#seguridad">Seguridad de la información</a></li>
            <li><a href="#disponibilidad">Disponibilidad y responsabilidad</a></li>
            <li><a href="#contenido">Contenido cargado y propiedad intelectual</a></li>
            <li><a href="#suspension">Suspensión de cuentas</a></li>
            <li><a href="#modificaciones">Modificaciones</a></li>
            <li><a href="#ley">Ley aplicable</a></li>
            <li><a href="#contacto">Contacto</a></li>
            <li><a href="#vigencia">Vigencia</a></li>
        </ol>
    </nav>

    <section id="objeto">
        <h2>1. Objeto y partes</h2>
        <p>
            Estos términos regulan el acceso y uso del portal web <strong>AsoJuntaSys</strong>, una plataforma
            digital para la gestión administrativa, documental y financiera de las Juntas de Acción Comunal (JAC),
            desarrollada como caso de estudio para la <?= htmlspecialchars($nombre_responsable) ?>.
        </p>
        <p>
            En este documento, «la plataforma» es AsoJuntaSys y «el usuario» es la persona autorizada que accede a ella.
        </p>
    </section>

    <section id="aceptacion">
        <h2>2. Aceptación de los términos</h2>
        <p>
            Al activar su cuenta, marcar la casilla de aceptación o iniciar sesión, el usuario declara que leyó,
            entendió y acepta estos términos y la política de tratamiento de datos personales de la sección 6.
            Si no está de acuerdo, no debe usar la plataforma.
        </p>
    </section>

    <section id="finalidad">
        <h2>3. Finalidad del sistema</h2>
        <p>
            AsoJuntaSys centraliza y organiza la información de la JAC en cuatro frentes: usuarios y roles,
            documentos y actas, agenda comunitaria, y registro de ingresos y egresos. Permite registrarla,
            consultarla, exportarla en PDF y Excel, y conservar el historial de las acciones realizadas.
        </p>
    </section>

    <section id="usuarios">
        <h2>4. Usuarios y cuentas</h2>
        <p>
            El acceso está restringido a personas autorizadas por la JAC. Las cuentas las crea y administra el
            Presidente General, quien asigna uno de los siguientes roles:
        </p>
        <ul>
            <li><strong>Presidente General:</strong> administra usuarios y consulta el registro de actividad.</li>
            <li><strong>Presidente de JAC:</strong> gestiona la agenda y consulta documentos, actas e informes.</li>
            <li><strong>Secretaría:</strong> gestiona documentos y actas.</li>
            <li><strong>Tesorería:</strong> registra y reporta los movimientos financieros.</li>
        </ul>
        <p>
            Cada rol solo accede a las funciones que le corresponden. El usuario es responsable de todo lo que se
            realice con su cuenta.
        </p>
    </section>

    <section id="responsabilidades">
        <h2>5. Responsabilidades del usuario</h2>
        <p>El usuario se compromete a:</p>
        <ul>
            <li>Dar información veraz y mantenerla actualizada.</li>
            <li>Guardar la confidencialidad de su contraseña y no compartirla con nadie.</li>
            <li>Cerrar sesión al terminar, sobre todo en equipos compartidos.</li>
            <li>Avisar de inmediato al administrador si sospecha que su cuenta fue usada sin su permiso.</li>
            <li>Cargar solo documentos e información que tenga derecho a manejar para los fines de la JAC.</li>
        </ul>
        <p>No está permitido:</p>
        <ul>
            <li>Usar la plataforma para fines ilegales o ajenos a la gestión de la JAC.</li>
            <li>Intentar acceder a funciones, cuentas o datos para los que no tiene autorización.</li>
            <li>Alterar, borrar o falsear registros para ocultar o distorsionar información.</li>
            <li>Afectar el funcionamiento de la plataforma, por ejemplo con cargas automatizadas o código malicioso.</li>
        </ul>
    </section>

    <section id="datos">
        <h2>6. Tratamiento de datos personales</h2>
        <p>
            El tratamiento de datos personales se rige por la Ley 1581 de 2012, el Decreto 1377 de 2013
            (compilado en el Decreto 1074 de 2015) y las normas que los modifiquen o complementen.
        </p>

        <h3>6.1 Responsable del tratamiento</h3>
        <p>
            El responsable es la <?= htmlspecialchars($nombre_responsable) ?>. El equipo que administra
            técnicamente la plataforma actúa como encargado del tratamiento y solo trata los datos para
            los fines aquí descritos.
        </p>

        <h3>6.2 Datos que se recolectan</h3>
        <ul>
            <li><strong>De la cuenta:</strong> nombre, correo electrónico, cargo, JAC a la que pertenece y contraseña
                (almacenada solo como hash, nunca en texto legible).</li>
            <li><strong>De uso:</strong> acciones realizadas en la plataforma, fecha y hora, y dirección IP.</li>
            <li><strong>De gestión comunitaria:</strong> los datos contenidos en los documentos, actas, agenda y
                registros financieros que los usuarios cargan, como nombres de asistentes y responsables.</li>
        </ul>
        <p>La plataforma no solicita datos sensibles. Si el usuario los incluye en un documento, es su responsabilidad hacerlo solo cuando sea necesario y esté autorizado.</p>

        <h3>6.3 Finalidades</h3>
        <ul>
            <li>Crear y administrar cuentas y controlar el acceso por roles.</li>
            <li>Gestionar los documentos, actas, agenda y movimientos financieros de la JAC.</li>
            <li>Enviar correos del sistema, como el de recuperación de contraseña.</li>
            <li>Mantener un historial de acciones para control interno y seguridad.</li>
            <li>Atender consultas, reclamos y requerimientos legales.</li>
        </ul>
        <p>Los datos no se venden ni se usan para fines comerciales o publicitarios.</p>

        <h3>6.4 Autorización</h3>
        <p>
            Al aceptar estos términos, el titular autoriza de forma previa, expresa e informada el tratamiento
            de sus datos para las finalidades indicadas. Puede revocar esta autorización y solicitar la
            supresión de sus datos, salvo cuando exista un deber legal o contractual de conservarlos.
        </p>

        <h3>6.5 Derechos del titular</h3>
        <p>El titular de los datos tiene derecho a:</p>
        <ul>
            <li>Conocer, actualizar y rectificar sus datos.</li>
            <li>Solicitar prueba de la autorización otorgada.</li>
            <li>Ser informado del uso que se ha dado a sus datos.</li>
            <li>Presentar quejas ante la Superintendencia de Industria y Comercio por infracciones a la ley.</li>
            <li>Revocar la autorización y solicitar la supresión de sus datos cuando proceda.</li>
            <li>Acceder de forma gratuita a sus datos personales.</li>
        </ul>
        <p>
            Para ejercerlos, puede escribir a
            <a href="mailto:<?= htmlspecialchars($correo_contacto) ?>"><?= htmlspecialchars($correo_contacto) ?></a>.
            Las consultas se responden en un máximo de 10 días hábiles y los reclamos en un máximo de
            15 días hábiles, contados desde su recepción; la ley permite prorrogar estos plazos en los casos que ella misma establece.
        </p>

        <h3>6.6 Conservación</h3>
        <p>
            Los datos se conservan mientras la cuenta esté activa y el tiempo necesario para cumplir las
            finalidades descritas y las obligaciones legales o de archivo de la JAC.
        </p>

        <h3>6.7 Proveedores y ubicación de los datos</h3>
        <p>
            Para operar, la plataforma usa proveedores de alojamiento en la nube y de envío de correo
            electrónico, que procesan datos por cuenta del responsable. Estos servicios pueden estar en
            servidores fuera de Colombia. Al aceptar estos términos, el usuario autoriza esa transmisión o
            transferencia de datos para el funcionamiento de la plataforma.
        </p>
    </section>

    <section id="auditoria">
        <h2>7. Registro de actividad</h2>
        <p>
            La plataforma registra las acciones relevantes de los usuarios (por ejemplo, crear, editar o eliminar
            registros), con el usuario responsable, la fecha y la dirección IP. Este historial solo lo consulta
            el Presidente General y sirve para control interno, seguridad y trazabilidad.
        </p>
    </section>

    <section id="seguridad">
        <h2>8. Seguridad de la información</h2>
        <p>
            La plataforma aplica medidas técnicas y administrativas para proteger la información, como el
            almacenamiento de contraseñas con hash, el control de acceso por roles, el bloqueo temporal tras
            varios intentos fallidos de ingreso y el cierre de sesión por inactividad.
        </p>
        <p>
            Ningún sistema informático es completamente infalible. La plataforma no puede garantizar la ausencia
            absoluta de fallos o accesos no autorizados, pero se compromete a actuar con diligencia para
            prevenirlos y a corregirlos cuando los detecte.
        </p>
    </section>

    <section id="disponibilidad">
        <h2>9. Disponibilidad del servicio y responsabilidad</h2>
        <p>
            La plataforma se ofrece en el estado en que se encuentra y puede tener interrupciones por
            mantenimiento, fallas de internet o de los proveedores de infraestructura. Se recomienda conservar
            copia de los documentos originales y de los soportes financieros de la JAC.
        </p>
        <p>
            Los administradores de la plataforma no responden por el contenido que los usuarios cargan ni por
            decisiones que la JAC tome con base en esa información, ni por daños derivados del uso indebido de
            las credenciales por parte del usuario.
        </p>
    </section>

    <section id="contenido">
        <h2>10. Contenido cargado y propiedad intelectual</h2>
        <p>
            Los documentos, actas y registros que se cargan pertenecen a la JAC o a sus autores. El usuario
            concede a la plataforma el permiso necesario para almacenarlos, procesarlos y mostrarlos a los
            usuarios autorizados. El software, el diseño y la marca AsoJuntaSys son de sus autores y no pueden
            copiarse ni distribuirse sin autorización.
        </p>
    </section>

    <section id="suspension">
        <h2>11. Suspensión de cuentas</h2>
        <p>
            El administrador puede suspender o eliminar una cuenta cuando el usuario incumpla estos términos,
            cuando termine su cargo en la JAC o cuando exista un riesgo para la seguridad de la información.
        </p>
    </section>

    <section id="modificaciones">
        <h2>12. Modificaciones</h2>
        <p>
            Estos términos pueden actualizarse para mejorar el servicio o cumplir nuevas normas. La versión
            vigente y su fecha estarán siempre indicadas al inicio de esta página. Si el cambio afecta de forma
            importante el tratamiento de datos personales, se pedirá de nuevo la aceptación del usuario.
        </p>
    </section>

    <section id="ley">
        <h2>13. Ley aplicable</h2>
        <p>
            Estos términos se rigen por las leyes de la República de Colombia. Cualquier diferencia se tratará
            primero de manera directa y, si no se resuelve, ante las autoridades competentes colombianas.
        </p>
    </section>

    <section id="contacto">
        <h2>14. Contacto</h2>
        <p>
            Para dudas sobre estos términos, el tratamiento de datos o el uso de la plataforma, escriba a
            <a href="mailto:<?= htmlspecialchars($correo_contacto) ?>"><?= htmlspecialchars($correo_contacto) ?></a>.
        </p>
    </section>

    <section id="vigencia">
        <h2>15. Vigencia</h2>
        <p>
            Estos términos rigen desde su publicación en la plataforma, en la versión
            <?= htmlspecialchars($version) ?> del <?= htmlspecialchars($fecha_actualizacion) ?>, y
            permanecen vigentes mientras no sean reemplazados por una versión posterior.
        </p>
    </section>

    <a class="volver" href="login.php">← Volver al inicio de sesión</a>

</main>
</body>
</html>
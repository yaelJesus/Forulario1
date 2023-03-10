<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Resultados</title>
        <style>
            body{background-color: #6c4675; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: large; color:whitesmoke}
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    </head>
    <body class="p-3 m-0 border-0 bd-example bd-example-row" style="background-color: #6c4675; color:whitesmoke">
        <div>
        <?php
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $tabla = $_POST["tabla"];
            $limiteI = $_POST["limiteI"];
            $limiteF = $_POST["limiteF"];
            $star = FALSE;
            $cantidad = 0;
            $puntos = 0;
            if ($limiteI <= $limiteF) {
                $cantidad = ($limiteF - $limiteI) + 1;
                for ($i = $limiteI; $i <= $limiteF; $i++) { 
                    $respuestaU = $_POST["resultado".$i];
                    $respuesta = ($tabla * $i);
                    $color = "danger";
                    $op = 75;
                    if ($i % 2 == 0) {
                        $op = 50;
                    }
                    if ($respuestaU == $respuesta) {
                        $color = "success";
                        $puntos++;
                    }
                        echo '
                        <div class="container">
                            <div class="row justify-content-md-center">
                                <div class="col-4 border-0 bg-primary bg-opacity-'.$op.' border-0 rounded-start-3" style="height: 55px;">'.$tabla.' x '.$i.'= </div>
                                <div class="col-2 border-0 bg-'.$color.' border-0 rounded-end-3" style="height: 55px;">Su respuesta: '.$respuestaU;
                                if ($color == "success") {
                                    echo'
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                    </svg>';
                                }
                                else {
                                    echo'
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>';
                                }
                        echo '
                            </div>
                            </div>
                        </div>';
                }
            }
            else{
                $cantidad = ($limiteI - $limiteF) + 1;
                $star = TRUE;
                for ($i = $limiteI; $i >= $limiteF; $i--) { 
                    $respuestaU = $_POST["resultado".$i];
                    $respuesta = ($tabla * $i);
                    $color = "danger";
                    $op = 75;
                    if ($i % 2 == 0) {
                        $op = 50;
                    }
                    if ($respuestaU == $respuesta) {
                        $color = "success";
                        $puntos++;
                    }
                    echo '
                    <div class="container">
                        <div class="row justify-content-md-center">
                            <div class="col-4 border-0 bg-primary bg-opacity-'.$op.' border-0 rounded-start-3" style="height: 55px;">'.$tabla.' x '.$i.'</div>
                            <div class="col-2 border-0 bg-'.$color.' border-0 rounded-end-3" style="height: 55px;">Su respuesta: '.$respuestaU.'<i class="bi bi-x-circle"></i></div>
                        </div>
                    </div>';
                }
            }
            $puntiacion = round(($puntos / $cantidad) * 100, 0);
            echo '<h3 class="text-center">Participante '.$nombre.'<br>Su calificacion total es: '.$puntiacion.' / 100</h3>';
            $enlace = mysqli_connect("localhost", "root", "", "tablamultiplicar");
            $query = "INSERT INTO usuario(Nombre,Tabla,RangoI,RangoF,Puntuacion,Correo) VALUES (?,?,?,?,?,?)";
            $sentencia = mysqli_prepare($enlace,$query);
            mysqli_stmt_bind_param($sentencia,"ssssis", $nombre,$tabla,$limiteI,$limiteF,$puntiacion,$correo);
            mysqli_stmt_execute($sentencia);
            mysqli_stmt_close($sentencia);
            mysqli_close($enlace);
            if ($star) {
                $x = $limiteI;
                $limiteI = $limiteF;
                $limiteF = $x;
            }
            # Cargar la biblioteca PHPMailer
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

            require 'PHPMailer/PHPMailer.php';
            require 'PHPMailer/SMTP.php';
            require 'PHPMailer/Exception.php';

            # Crear una instancia de PHPMailer
            $mail = new PHPMailer(true);

            try {
                # Configurar el servidor SMTP y las credenciales de autenticación
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = /*'i18050485@monclova.tecnm.mx'*/'yael_jrm@hotmail.es';
                $mail->Password = /*'Porqueno0'*/'soloseknosenada1';
                $mail->SMTPSecure = 'STARTTLS';
                $mail->Port = 587;

                # Configurar los detalles del correo electrónico
                $mail->setFrom('yael_jrm@hotmail.es', 'Tablas de Multiplicar');
                $mail->addAddress($correo, $nombre);
                $mail->isHTML(true);
                $mail->Subject = 'Puntaje logrado';
                $mail->Body    = 'La puntuacion conseguida por practicar la tabla del <b>' . $tabla . '</b> entre el rango <b>' . $limiteI . '</b> al <b>' . $limiteF . '</b> es de: <b>'.$puntiacion.'</b>';
                
                #Enviar el correo electrónico
                if($mail->send()) {
                    echo 'El correo se ha enviado correctamente.';
                } else {
                    echo 'Ha ocurrido un error al enviar el correo: ' . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                echo 'Mensaje ' . $mail->ErrorInfo;
            }
        ?>
        <form action="Practica1.html" method="post">
            <div class="d-grid gap-2 col-6 mx-auto">
                <button class="btn btn-outline-light" type="submit" value="Inicio" style="font-size:xx-large;">Inicio
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>
                    </svg>
                </button>
            </div>
        </form>
        </div>
    </body>
</html>

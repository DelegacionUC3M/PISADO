<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> <?= $title ?> </title>

        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="/pisado/assets/css/normalize.min.css">
        <link rel="stylesheet" href="/pisado/assets/css/main.css">

        <script src="/pisado/assets/js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header>
            <div class="wrapper">

                <a href="http://delegacion.uc3m.es"> <img src="/pisado/assets/img/delegacion.png" /> </a>
                <h1>PISADO</h1>

                div
                <?php if ($user) { ?>
                    <span>Hola, <?= $user->name; ?></span>
                    <a href="inicio/logout">Salir</a>
                <?php } ?>

            </div>
        </header>

        <div class="main wrapper">
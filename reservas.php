<!--© Joan Aneas-->
<!-- Header DINAMICO -->
<?php require_once "dynamo/header-dinamico.php"; ?>

<body class="colorPrincipalBg fuenteTexto">

<!-- Nav DINAMICO -->
<?php require_once "dynamo/nav-dinamico.php"; ?>
<main>
    <section class="frame reservas">
        <div class="header-libros">
        <span>Les teves reserves apareixeran aquí.</span>
        </div>   
        <div class="tableContainer">
            <table id="reservasTable" class="tablaUsuarios">
                <tr>
                    <th>Reserva</th>
                    <th>Exemplar</th>
                    <th>Data Inici</th>
                    <th>Data Fi</th>
                    <th>Estat</th>
                    <th>Demanar prèstec</th>
                </tr>
            </table>
        </div>
    </section>
</main>

<!-- Footer DINAMICO -->
<?php require_once "dynamo/footer-dinamico.php"; ?>
</body>
</html> 
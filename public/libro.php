<!--© Joan Aneas-->
<!-- Header DINAMICO -->
<?php require_once "dynamo/header-dinamico.php"; ?>

<body class="colorPrincipalBg fuenteTexto">

    <!-- Nav DINAMICO -->
    <?php require_once "dynamo/nav-dinamico.php"; ?>
    <main>
        <section class="frame vistaLibro">
            <div class="header-libros">
                <h3 class="fuenteH1">
                    <span id="tituloLibro"></span>&nbsp;📖
                    <img id="min-qr" class="min-qr" src="./media/icons/qr-code.png" width="20" height="20">
                </h3>
            </div>
            <div class="container">
                <aside class="aside-image">
                    <img id="libroImagen" src="./media/sistema/pruebas.jpg" class="book-image">
                </aside>
                <aside class="aside-text">
                    <p><span class="fuenteH1">👤Autor:</span>&nbsp;<span id="autorLibro"></span></p>
                    <p><span class="fuenteH1">🧩Categoria:</span>&nbsp;<span id="categoria"></span< /p>
                            <p><span class="fuenteH1">🎓Nivell:</span>&nbsp;<span id="nivell"></span></p>
                            <p><span class="fuenteH1">💬Resum:</span>&nbsp;<span id="resumLibro"></span></p>
                            <p><span class="fuenteH1">⭐Estrelles:</span>&nbsp;
                            <div class="estrellas">
                                <img src="./media/icons/star-grey.png" width="20" height="20" id="starIcon"
                                    class="star">
                                <img src="./media/icons/star-grey.png" width="20" height="20" id="starIcon"
                                    class="star">
                                <img src="./media/icons/star-grey.png" width="20" height="20" id="starIcon"
                                    class="star">
                                <img src="./media/icons/star-grey.png" width="20" height="20" id="starIcon"
                                    class="star">
                                <img src="./media/icons/star-grey.png" width="20" height="20" id="starIcon"
                                    class="star">
                            </div>
                            <span id="starsToast"></span>
                    </p>
                    <button id="reservar" class="botonUniversal">Reservar</button>
                </aside>
            </div>

        </section>

        <section class="frame vistaLibro">
            <h3 class="fuenteH1">Comentaris</h3>
            <div class="comentarContainer">
                <textarea id="comentario" class="comentarioSelf fuenteTexto"
                    placeholder="Escriu el teu comentari"></textarea>
                <p id="comentarioToast" style="display:none;"></p>
                <button id="enviar-comentario" class="botonUniversal">Enviar</button>
            </div>

            <div id="comentarios" class="comentarios">
                <!-- Los comentarios se llenarán con JS -->
            </div>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    </main>
    <div class="popup" style="display:none;" id="popupQR">
        <div class="popup-content">
            <section class="frame">
                <a id="closeQR" class="close">&times;</a>
                <p class="fuenteH1" style="text-align:center;">Escaneja el QR del Llibre</p>
                <div id="qrcode-container" class="qr">
                    <div id="qrcode" class="qr"></div>
                    <img id="qr-image" class="qr" src="" alt="imatge centre qr" width="20" height="20">
                </div>
                <p class="fuenteH1" style="text-align:center;">Compartir</p>
                <div class="compartir-qr">
                    <a id="share-copy"><img src="./media/icons/copy-link.png"></a>
                    <a id="share-whatsapp"><img src="./media/icons/whatsapp.png"></a>
                    <a id="share-twitter"><img src="./media/icons/twitter.png"></a>
                    <a id="share-facebook"><img src="./media/icons/facebook.png"></a>
                </div>
            </section>
            <section id="toast-frame" class="frame toast" style="display:none;">
                <p id="toast">Copiat al portapapers</p>
            </section>
        </div>
    </div>
    <div class="popup" style="display:none;" id="popupReserva">
        <div class="popup-content">
            <section class="frame">
                <a id="closeReserva" class="close">&times;</a>
                <p class="fuenteH1" style="text-align:center;">Reservar</p>
                <div id="calendar-container">
                    <div id="calendar-header">
                        <button id="prev-month" class="botonUniversal">&laquo;</button>
                        <h2 id="month-year"></h2>
                        <button id="next-month" class="botonUniversal">&raquo;</button>
                    </div>
                    <table id="calendar" class="calendar">
                        <thead>
                            <tr>
                                <th>D</th>
                                <th>L</th>
                                <th>M</th>
                                <th>X</th>
                                <th>J</th>
                                <th>V</th>
                                <th>S</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body">
                            <!-- Las celdas del calendario se llenarán con JS -->
                        </tbody>
                    </table>
                </div>
                <p id="reserva-holder"></p>
                <button id="reservar-libro" class="botonUniversal">Reservar</button>
            </section>
        </div>
    </div>

    <!-- Footer DINAMICO -->
    <?php require_once "dynamo/footer-dinamico.php"; ?>
</body>

</html>
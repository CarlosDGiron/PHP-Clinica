<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Titulo del Módulo Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="row">
                        <div class="col-12 mb-0">
                            <div class="bg-transparent rounded d-flex align-items-center px-2">
                                <i id="moduleIcon"></i>
                                <h4 id="moduleTitle" class="mb-0"></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Titulo del Módulo Fin -->


                <!-- Contenido de Bievenida Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <h1 id="greetingTitle" class="mb-0"></h1>
                        </div>
                        <img src="<?= URL_ROUTE; ?>/img/home.jpg" class="img-fluid w-100" alt="Inicio" style="object-fit: cover; max-width: 950px;"> <!-- Ajusta el valor de max-width según tus necesidades -->
                    </div>
                </div>
                <!-- Contenido de Bievenida Fin -->

                <!-- Noticias Salud Inicio -->
                <div class="container-fluid pt-2 px-4 pb-5">
                    <div class="news-section bg-light rounded p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0">Noticias de Salud</h5>
                            <small class="text-muted">Fuente externa • actualizado al cargar</small>
                        </div>
                        <div id="newsGrid" class="row g-3 g-md-4">
                            <!-- Tarjetas generadas por JS -->
                        </div>
                    </div>
                </div>
                <!-- Noticias Salud Fin -->
            </div>
            <!-- Contenido de la Página Fin -->

<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>

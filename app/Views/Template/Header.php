<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= SITE_NAME; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <script>
        (function(){
            try {
                var t = localStorage.getItem('theme');
                if (t !== 'light' && t !== 'dark') t = 'dark';
                document.documentElement.classList.add(t === 'dark' ? 'theme-dark' : 'theme-light');
            } catch(e) {
                document.documentElement.classList.add('theme-dark');
            }
        })();
    </script>

    <!-- Favicon -->
    <link href="<?= URL_ROUTE; ?>/img/favicon.svg" rel="icon" type="image/svg+xml">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Libraries Stylesheet -->
    <link href="<?= URL_ROUTE; ?>/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="<?= URL_ROUTE; ?>/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?= URL_ROUTE; ?>/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="<?= URL_ROUTE; ?>/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center flex-column">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
            <div class="text-center mt-3">
                <p> Cargando...</p>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="<?= URL_ROUTE; ?>" class="navbar-brand mx-4">
                    <h3 class="brand-title mb-3 mb-lg-0">
                        <img class="image me-2" src="<?= URL_ROUTE; ?>/img/logo.svg" alt="Clínica">Clínica
                    </h3>
                </a>
                <div class="w-100 mb-2">
                    <h5 id="branchName" class="text-primary text-center text-wrap"></h5>
                </div>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <i class="bi bi-person-circle user-icons"></i>
                    </div>
                    <div class="ms-3">
                        <h6 id="navUserName" class="mb-0"></h6>
                        <span id="navRoleName"></span>
                    </div>
                </div>
                <div class="navbar-nav w-100 navbar-modules">
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="<?= URL_ROUTE; ?>" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="brand-title"><img class="image mt-2" src="<?= URL_ROUTE; ?>/img/logo.svg" alt="Clínica"></h2>
                </a>

                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item me-2 d-flex align-items-center">
                        <button id="themeToggle" type="button" class="btn btn-sm btn-outline-secondary rounded-circle theme-toggle" title="Cambiar tema" aria-label="Cambiar tema">
                            <i id="themeToggleIcon" class="bi bi-moon-stars"></i>
                        </button>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span id="dropUserName" class="d-none d-lg-inline-flex"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-2 rounded-0 rounded-bottom m-0">
                            <a href="<?= URL_ROUTE; ?>" id="logoutLink" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i>Salir</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->
             
        
            <!-- Alerta HTML Inicio-->
            <div id="errorMessage" class="d-flex justify-content-between position-fixed top-0 end-0 m-3 fade" role="alert">
                <i class="bi bi-bell-fill me-2"></i><span id="errorText"></span>
                <i id="closeButton" class="bi bi-x fs-5 ms-1" role="button" aria-label="Close"></i>
            </div>
            <!-- Alerta HTML Fin -->

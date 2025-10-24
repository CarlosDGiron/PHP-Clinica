<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Modal de Insertar Diagnóstico Inicio -->
                <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Agregar Diagnóstico</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="insertForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModPatient" class="form-label mb-0">Paciente</label>
                                            <select name="insModPatient" class="form-control form-select" id="insModPatient" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModDoctor" class="form-label mb-0">Doctor</label>
                                            <select name="insModDoctor" class="form-control form-select" id="insModDoctor" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModBranch" class="form-label mb-0">Sucursal</label>
                                            <select name="insModBranch" class="form-control form-select" id="insModBranch" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModVisitDate" class="form-label mb-0">Fecha de Visita</label>
                                            <input type="date" class="form-control" id="insModVisitDate" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModDiagnosis" class="form-label mb-0">Diagnóstico</label>
                                            <input type="text" class="form-control" id="insModDiagnosis" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModExams" class="form-label mb-0">Exámenes</label>
                                            <div class="dropdown">
                                                <button class="form-control dropdown-toggle" type="button" id="insModExams" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Seleccione opciones
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="insModExams">
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModTreatmentPlan" class="form-label mb-0">Tratamiento</label>
                                            <input type="text" class="form-control" id="insModTreatmentPlan" maxlength="50">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-primary" form="insertForm">Guardar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Insertar Diagnóstico Fin -->


                <!-- Modal de Actualizar Diagnóstico Inicio -->
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="updateTitle">Actualizar Diagnóstico</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="updateForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModPatient" class="form-label mb-0">Paciente</label>
                                            <select name="updModPatient" class="form-control form-select" id="updModPatient" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModDoctor" class="form-label mb-0">Doctor</label>
                                            <select name="updModDoctor" class="form-control form-select" id="updModDoctor" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModBranch" class="form-label mb-0">Sucursal</label>
                                            <select name="updModBranch" class="form-control form-select" id="updModBranch" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModVisitDate" class="form-label mb-0">Fecha de Visita</label>
                                            <input type="date" class="form-control" id="updModVisitDate" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModDiagnosis" class="form-label mb-0">Diagnóstico</label>
                                            <input type="text" class="form-control" id="updModDiagnosis" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModExams" class="form-label mb-0">Exámenes</label>
                                            <div class="dropdown">
                                                <button class="form-control dropdown-toggle" type="button" id="updModExams" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Seleccione opciones
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="updModExams">
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModTreatmentPlan" class="form-label mb-0">Tratamiento</label>
                                            <input type="text" class="form-control" id="updModTreatmentPlan" maxlength="50">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-primary" form="updateForm">Guardar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Actualizar Diagnóstico Fin -->
                
                
                <!-- Modal de Eliminar Diagnóstico Inicio -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="deleteTitle">Eliminar Diagnóstico</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="deleteForm">
                                    <div class="row">
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Paciente: </span><span id="delModPatient"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Doctor: </span><span id="delModDoctor"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Sucursal: </span><span id="delModBranch"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Fecha: </span><span id="delModVisitDate"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Diagóstico: </span><span id="delModDiagnosis"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Exámenes: </span><span id="delModExams"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Tratamiento: </span><span id="delModTreatmentPlan"></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger" form="deleteForm">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Eliminar Diagnóstico Fin -->

                <!-- Titulo del Módulo Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="row">
                        <div id="iconClassTitle" class="col-12 col-sm-5 mb-0">
                            <div class="bg-transparent rounded d-flex align-items-center px-2">
                                <i id="moduleIcon"></i>
                                <h4 id="moduleTitle" class="mb-0"></h4>
                            </div>
                        </div>
                        <div id="buttonClassTitle" class="col-12 col-sm-7 d-flex align-items-center justify-content-start justify-content-sm-end">
                            <div class="bg-transparent rounded d-flex">
                                <div class="rounded">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar">
                                </div>
                                <div id="addButton"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Titulo del Módulo Fin -->

                <!-- Historial Clínico Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Historial Clínico</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start table-bordered table-hover mb-0">
                                <thead id="tableHead">
                                    <tr class="text-dark align-middle">
                                        <th scope="col">Paciente</th>
                                        <th scope="col">Doctor</th>
                                        <th scope="col">Sucursal</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Diagnóstico</th>
                                        <th scope="col">Exámenes</th>
                                        <th scope="col">Tratamiento</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Historial Clínico Fin -->
            </div>
            <!-- Contenido de la Página Fin -->
<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>
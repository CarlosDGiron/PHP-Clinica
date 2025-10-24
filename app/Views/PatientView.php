<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Modal de Insertar Paciente Inicio -->
                <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Asignar Habitación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="roomForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="roomModBranch" class="form-label mb-0">Sucursal</label>
                                            <select name="roomModBranch" class="form-control form-select" id="roomModBranch">
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="roomModRoom" class="form-label mb-0">Habitación</label>
                                            <select name="roomModRoom" class="form-control form-select" id="roomModRoom" required>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-primary" form="roomForm">Guardar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Insertar Paciente Fin -->


                <!-- Modal de Insertar Paciente Inicio -->
                <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Agregar Paciente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="insertForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModFirstName" class="form-label mb-0">Nombre</label>
                                            <input type="text" class="form-control" id="insModFirstName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModLastName" class="form-label mb-0">Apellido</label>
                                            <input type="text" class="form-control" id="insModLastName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModEmail" class="form-label mb-0">Correo</label>
                                            <input type="email" class="form-control" id="insModEmail" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModGender" class="form-label mb-0">Género</label>
                                            <select name="insModGender" class="form-control form-select" id="insModGender" required>
                                                <option>Masculino</option>
                                                <option>Femenino</option>
                                                <option>Otro</option>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModAddress" class="form-label mb-0">Dirección</label>
                                            <input type="text" class="form-control" id="insModAddress" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModPhone" class="form-label mb-0">Teléfono</label>
                                            <input type="tel" class="form-control" id="insModPhone" pattern="[0-9]{8}" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModBirthDate" class="form-label mb-0">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" id="insModBirthDate" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModStatus" class="form-label mb-0">Estado</label>
                                            <select name="insModStatus" class="form-control form-select" id="insModStatus" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
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
                <!-- Modal de Insertar Paciente Fin -->


                <!-- Modal de Actualizar Paciente Inicio -->
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="updateTitle">Actualizar Paciente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="updateForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModPatientCode" class="form-label mb-0">Código</label>
                                            <input type="text" class="form-control" id="updModPatientCode" maxlength="50" disabled>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModFirstName" class="form-label mb-0">Nombre</label>
                                            <input type="text" class="form-control" id="updModFirstName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModLastName" class="form-label mb-0">Apellido</label>
                                            <input type="text" class="form-control" id="updModLastName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModEmail" class="form-label mb-0">Correo</label>
                                            <input type="email" class="form-control" id="updModEmail" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModGender" class="form-label mb-0">Género</label>
                                            <select name="updModGender" class="form-control form-select" id="updModGender" required>
                                                <option>Masculino</option>
                                                <option>Femenino</option>
                                                <option>Otro</option>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModAddress" class="form-label mb-0">Dirección</label>
                                            <input type="text" class="form-control" id="updModAddress" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModPhone" class="form-label mb-0">Teléfono</label>
                                            <input type="tel" class="form-control" id="updModPhone" pattern="[0-9]{8}" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModBirthDate" class="form-label mb-0">Fecha de Nacimiento</label>
                                            <input type="date" class="form-control" id="updModBirthDate" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModStatus" class="form-label mb-0">Estado</label>
                                            <select name="updModStatus" class="form-control form-select" id="updModStatus" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
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
                <!-- Modal de Actualizar Paciente Fin -->
                
                
                <!-- Modal de Eliminar Paciente Inicio -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="deleteTitle">Eliminar Paciente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="deleteForm">
                                    <div class="row">
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Código: </span><span id="delModPatientCode"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Nombre: </span><span id="delModName"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Correo: </span><span id="delModEmail"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Genero: </span><span id="delModGender"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Dirección: </span><span id="delModAddress"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Teléfono: </span><span id="delModPhone"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Fecha de Nacimiento: </span><span id="delModBirthDate"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Estado: </span><span id="delModStatus"></span>
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
                <!-- Modal de Eliminar Paciente Fin -->

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

                <!-- Pacientes Registrados Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Pacientes Registrados</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start table-bordered table-hover mb-0">
                                <thead id="tableHead">
                                    <tr class="text-dark align-middle">
                                        <th scope="col">Código</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Correo</th>
                                        <th scope="col">Género</th>
                                        <th scope="col">Dirección</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Fecha de Nacimiento</th>
                                        <th scope="col">Habitación</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Pacientes Registrados Fin -->
            </div>
            <!-- Contenido de la Página Fin -->
<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>
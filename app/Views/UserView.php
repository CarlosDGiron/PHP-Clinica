<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Modal de Insertar Usuario Inicio -->
                <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Agregar Usuario</h5>
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
                                            <input type="text" class="form-control" id="insModLastName" maxlength="50">
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModUsername" class="form-label mb-0">Usuario</label>
                                            <input type="text" class="form-control" id="insModUsername" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModRole" class="form-label mb-0">Rol</label>
                                            <select name="insModRole" class="form-control form-select" id="insModRole" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModEmployee" class="form-label mb-0">Empleado</label>
                                            <select name="insModEmployee" class="form-control form-select" id="insModEmployee">
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModStatus" class="form-label mb-0">Estado</label>
                                            <select name="insModStatus" class="form-control form-select" id="insModStatus" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModPassword" class="form-label mb-0">Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="insModPassword" maxlength="50" required>
                                                <button class="btn btn-outline-secondary" type="button" id="insModTogglePassword" style="border-color: #ced4da; color: #757575;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModConfirmPassword" class="form-label mb-0">Confirmar Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="insModConfirmPassword" maxlength="50" required>
                                                <button class="btn btn-outline-secondary" type="button" id="insModToggleConfirmPassword"  style="border-color: #ced4da; color: #757575;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
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
                <!-- Modal de Insertar Usuario Fin -->


                <!-- Modal de Actualizar Usuario Inicio -->
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="updateTitle">Actualizar Usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="updateForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModFirstName" class="form-label mb-0">Nombre</label>
                                            <input type="text" class="form-control" id="updModFirstName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModLastName" class="form-label mb-0">Apellido</label>
                                            <input type="text" class="form-control" id="updModLastName" maxlength="50">
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModRole" class="form-label mb-0">Rol</label>
                                            <select name="updModRole" class="form-control form-select" id="updModRole" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModEmployee" class="form-label mb-0">Empleado</label>
                                            <select name="updModEmployee" class="form-control form-select" id="updModEmployee">
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModStatus" class="form-label mb-0">Estado</label>
                                            <select name="updModStatus" class="form-control form-select" id="updModStatus" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModPassword" class="form-label mb-0">Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="updModPassword" maxlength="50">
                                                <button class="btn btn-outline-secondary" type="button" id="updModTogglePassword"  style="border-color: #ced4da; color: #757575;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
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
                <!-- Modal de Actualizar Usuario Fin -->
                
                
                <!-- Modal de Eliminar Usuario Inicio -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="deleteTitle">Eliminar Usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="deleteForm">
                                    <div class="row">
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Nombre: </span><span id="delModName"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Usuario: </span><span id="delModUsername"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Empleado: </span><span id="delModEmployee"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Rol: </span><span id="delModRole"></span>
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
                <!-- Modal de Eliminar Usuario Fin -->

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

                <!-- Usuarios Registrados Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Usuarios Registrados</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start table-bordered table-hover mb-0">
                                <thead id="tableHead">
                                    <tr class="text-dark align-middle">
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Usuario</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Usuarios Registrados Fin -->
            </div>
            <!-- Contenido de la Página Fin -->
<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>
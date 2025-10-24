<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Modal de Insertar Sucursal Inicio -->
                <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Agregar Sucursal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="insertForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModName" class="form-label mb-0">Nombre</label>
                                            <input type="text" class="form-control" id="insModName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModCity" class="form-label mb-0">Ciudad</label>
                                            <input type="text" class="form-control" id="insModCity" maxlength="50" required>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal de Insertar Sucursal Fin -->


                <!-- Modal de Actualizar Sucursal Inicio -->
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="updateTitle">Actualizar Sucursal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="updateForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModName" class="form-label mb-0">Nombre</label>
                                            <input type="text" class="form-control" id="updModName" maxlength="50" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModCity" class="form-label mb-0">Ciudad</label>
                                            <input type="text" class="form-control" id="updModCity" maxlength="50" required>
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
                <!-- Modal de Actualizar Sucursal Fin -->
                
                
                <!-- Modal de Eliminar Sucursal Inicio -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="deleteTitle">Eliminar Sucursal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="deleteForm">
                                    <div class="row">
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Nombre: </span><span id="delModName"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Ciudad: </span><span id="delModCity"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Dirección: </span><span id="delModAddress"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Teléfono: </span><span id="delModPhone"></span>
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
                <!-- Modal de Eliminar Sucursal Fin -->

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

                <!-- Sucursales Registradas Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Sucursales Registradas</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start table-bordered table-hover mb-0">
                                <thead id="tableHead">
                                    <tr class="text-dark align-middle">
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Ciudad</th>
                                        <th scope="col">Dirección</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Sucursales Registradas Fin -->
            </div>
            <!-- Contenido de la Página Fin -->
<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>
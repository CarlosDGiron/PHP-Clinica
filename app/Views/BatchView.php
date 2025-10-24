<?php require_once APP_ROUTE."/Views/Template/Header.php"; ?>
            <!-- Contenido de la Página Inicio -->
            <div id="content">
                <!-- Modal de Insertar Lote Inicio -->
                <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="insertTitle">Agregar Lote</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="insertForm" data-info="">

                                    <div class="row">
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModMedicine" class="form-label mb-0">Medicina</label>
                                            <select name="insModMedicine" class="form-control form-select" id="insModMedicine" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModSupplier" class="form-label mb-0">Proveedor</label>
                                            <select name="insModSupplier" class="form-control form-select" id="insModSupplier" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModBranch" class="form-label mb-0">Sucursal</label>
                                            <select name="insModBranch" class="form-control form-select" id="insModBranch"  required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModPurchasePrice" class="form-label mb-0">Precio Compra</label>
                                            <input type="number" class="form-control" id="insModPurchasePrice" step="0.01" min="0" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModQuantity" class="form-label mb-0">Cantidad</label>
                                            <input type="number" class="form-control" id="insModQuantity" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="insModExpirationDate" class="form-label mb-0">Expiración</label>
                                            <input type="date" class="form-control" id="insModExpirationDate" maxlength="50"  required>
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
                <!-- Modal de Insertar Lote Fin -->


                <!-- Modal de Actualizar Lote Inicio -->
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="updateTitle">Actualizar Lote</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="updateForm" data-info="">
                                    <div class="row">

                                        <div div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModBatchId" class="form-label mb-0">No. Lote</label>
                                            <input type="text" class="form-control" id="updModBatchId" maxlength="50" disabled>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModMedicine" class="form-label mb-0">Medicina</label>
                                            <select name="updModMedicine" class="form-control form-select" id="updModMedicine" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModSupplier" class="form-label mb-0">Proveedor</label>
                                            <select name="updModSupplier" class="form-control form-select" id="updModSupplier" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModBranch" class="form-label mb-0">Sucursal</label>
                                            <select name="updModBranch" class="form-control form-select" id="updModBranch" required>
                                            </select>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModPurchasePrice" class="form-label mb-0">Precio Compra</label>
                                            <input type="number" class="form-control" id="updModPurchasePrice" step="0.01" min="0" required>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModQuantity" class="form-label mb-0">Cantidad</label>
                                            <input type="number" class="form-control" id="updModQuantity" disabled>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModCreatedAt" class="form-label mb-0">Ingreso</label>
                                            <input type="date" class="form-control" id="updModCreatedAt" disabled>
                                        </div>
                                        <div class="mb-2 mb-sm-3 px-4">
                                            <label for="updModExpirationDate" class="form-label mb-0">Expiración</label>
                                            <input type="date" class="form-control" id="updModExpirationDate" maxlength="50" required>
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
                <!-- Modal de Actualizar Lote Fin -->
                
                
                <!-- Modal de Eliminar Lote Inicio -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center" id="deleteTitle">Eliminar Lote</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="deleteForm">
                                    <div class="row">
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>No. Lote: </span><span id="delModBatchId"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Medicina: </span><span id="delModMedicine"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Proveedor: </span><span id="delModSupplier"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Sucursal: </span><span id="delModBranch"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Precio Compra: </span><span id="delModPurchasePrice"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Cantidad: </span><span id="delModQuantity"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Ingreso: </span><span id="delModCreatedAt"></span>
                                        </div>
                                        <div class="mb-0 mb-sm-1 col-12">
                                            <span>Expiración: </span><span id="delModExpirationDate"></span>
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
                <!-- Modal de Eliminar Lote Fin -->

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

                <!-- Lotes Registrados Inicio -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-light text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Lotes Registrados</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start table-bordered table-hover mb-0">
                                <thead id="tableHead">
                                    <tr class="text-dark align-middle">
                                        <th scope="col">No. Lote</th>
                                        <th scope="col">Médicina</th>
                                        <th scope="col">Proveedor</th>
                                        <th scope="col">Sucursal</th>
                                        <th scope="col">Precio Compra</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Ingreso</th>
                                        <th scope="col">Expiración</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Lotes Registrados Fin -->
            </div>
            <!-- Contenido de la Página Fin -->
<?php require_once APP_ROUTE."/Views/Template/Footer.php"; ?>
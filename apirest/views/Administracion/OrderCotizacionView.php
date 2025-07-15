<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <a href="javascript:history.back()" class="btn btn-default mb-2"><i class="fa fa-arrow-left"></i> Regresar</a>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cotización de Orden</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><b>Cliente</b></label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($order->customer_full_name) ?>" readonly>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Link tienda</th>
                                    <th>Link alibaba</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($item->product_image)): ?>
                                            <img src="<?= htmlspecialchars($item->product_image) ?>" style="max-width:120px;max-height:120px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item->product_name) ?></td>
                                    <td><?= htmlspecialchars($item->quantity) ?></td>
                                    <td>S/. <?= number_format($item->unit_price, 2) ?></td>
                                    <td>
                                        <?php if (!empty($item->url_tienda)): ?>
                                            <a href="<?= htmlspecialchars($item->url_tienda) ?>" target="_blank">Ver tienda</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->url_alibaba)): ?>
                                            <a href="<?= htmlspecialchars($item->url_alibaba) ?>" target="_blank">Ver Alibaba</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 
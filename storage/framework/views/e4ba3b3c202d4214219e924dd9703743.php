<?php $__env->startSection('title', 'ERPREV Sales Data | Rhymes Platform'); ?>

<?php $__env->startSection('page-title', 'ERPREV Sales Data'); ?>

<?php $__env->startSection('page-description', 'Sales transactions from ERPREV system'); ?>

<?php $__env->startSection('content'); ?>
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Sales Data</h3>
                        <div class="nk-block-des text-soft">
                            <p>Sales transactions synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="<?php echo e(route('admin.erprev.inventory')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.products')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-grid-add"></em><span>Products</span></a></li>
                                    <li><a href="<?php echo e(route('admin.erprev.summary')); ?>" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <form method="GET" action="<?php echo e(route('admin.erprev.sales')); ?>" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="product_id">Product ID</label>
                                        <input type="text" class="form-control" id="product_id" name="product_id" value="<?php echo e($filters['product_id'] ?? ''); ?>" placeholder="ERPREV Product ID">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="<?php echo e(route('admin.erprev.sales')); ?>" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if(count($salesData) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                            <th>Warehouse</th>
                                            <th>Units</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $salesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($sale['SN'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <strong><?php echo e($sale['Name'] ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo e($sale['Barcode'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['Category'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($sale['WareHouse'] ?? 'N/A'); ?></td>
                                                <td><?php echo e(number_format((float)($sale['UnitsInStock'] ?? 0))); ?></td>
                                                <td><?php echo $sale['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format((float)($sale['SellingPrice'] ?? 0), 2)); ?></td>
                                                <td><?php echo $sale['CurrencySymbol'] ?? '&#x20A6;'; ?><?php echo e(number_format(((float)($sale['SellingPrice'] ?? 0)) * ((float)($sale['UnitsInStock'] ?? 0)), 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <em class="icon ni ni-file-text" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No sales data found</p>
                                <?php if(empty($filters)): ?>
                                    <p class="text-muted">Try adjusting your filters or check the ERPREV connection</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/erprev/sales.blade.php ENDPATH**/ ?>
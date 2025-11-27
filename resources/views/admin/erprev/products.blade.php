@extends('layouts.admin')

@section('title', 'ERPREV Product Listings | Rhymes Platform')

@section('page-title', 'ERPREV Product Listings')

@section('page-description', 'Product catalog from ERPREV system')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">ERPREV Product Listings</h3>
                        <div class="nk-block-des text-soft">
                            <p>Product catalog synchronized from ERPREV system</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.erprev.sales') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-tranx"></em><span>Sales</span></a></li>
                                    <li><a href="{{ route('admin.erprev.inventory') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-package"></em><span>Inventory</span></a></li>
                                    <li><a href="{{ route('admin.erprev.summary') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-bar-chart"></em><span>Summary</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-full">
                    <div class="card-inner">
                        <form method="GET" action="{{ route('admin.erprev.products') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="product_code">Product Code</label>
                                        <input type="text" class="form-control" id="product_code" name="product_code" value="{{ $filters['product_code'] ?? '' }}" placeholder="ISBN or Product Code">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="category">Category</label>
                                        <input type="text" class="form-control" id="category" name="category" value="{{ $filters['category'] ?? '' }}" placeholder="Product Category">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary me-2"><em class="icon ni ni-search"></em><span>Filter</span></button>
                                            <a href="{{ route('admin.erprev.products') }}" class="btn btn-light"><em class="icon ni ni-reload"></em><span>Reset</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @if(count($products) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Category</th>
                                            <th>Warehouse</th>
                                            <th>Units In Stock</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr>
                                                <td>{{ $product['SN'] ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $product['Name'] ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $product['Barcode'] ?? 'N/A' }}</td>
                                                <td>{{ $product['Category'] ?? 'N/A' }}</td>
                                                <td>{{ $product['WareHouse'] ?? 'N/A' }}</td>
                                                <td>{{ number_format((float)($product['UnitsInStock'] ?? 0)) }}</td>
                                                <td>{!! $product['CurrencySymbol'] ?? '&#x20A6;' !!}{{ number_format((float)($product['SellingPrice'] ?? 0), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <em class="icon ni ni-grid-add" style="font-size: 48px; opacity: 0.3;"></em>
                                <p class="mt-3">No products found</p>
                                @if(empty($filters))
                                    <p class="text-muted">Try adjusting your filters or check the ERPREV connection</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
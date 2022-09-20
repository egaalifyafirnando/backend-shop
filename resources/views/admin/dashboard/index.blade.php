@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="container-fluid">
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold">PENDAPATAN BULAN INI</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <h5>{{ moneyFormat($revenueMonth) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold">PENDAPATAN TAHUN INI</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <h5>{{ moneyFormat($revenueYear) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold">SEMUA PENDAPATAN</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <h5>{{ moneyFormat($revenueAll) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pending Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2" style="border-left: 5px solid #3a78a8;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold mb-1" style="color: #3a78a8">PENDING</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-circle-notch fa-spin fa-2x" style="color: #3a78a8"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2" style="border-left: 5px solid #3F7B70;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold mb-1" style="color:#3F7B70">SUCCESS</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $success }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x" style="color:#3F7B70"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expired Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2" style="border-left: 5px solid #FECD51;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold mb-1" style="color:#FECD51">EXPIRED</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expired }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x" style="color:#FECD51"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Failed Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2" style="border-left: 5px solid #911F27;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold mb-1" style="color:#911F27">FAILED</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $failed }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x" style="color:#911F27"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app', ['title' => 'Orders'])

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> ORDERS</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.order.index') }}" method="GET">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="q" placeholder="cari berdasarkan no. invoice">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-green"><i class="fa fa-search"></i> CARI</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;width: 6%">NO.</th>
                                        <th scope="col" style="width: 19%">NO. INVOICE</th>
                                        <th scope="col" style="width: 35%">NAMA LENGKAP</th>
                                        <th scope="col">TOTAL PEMBAYARAN</th>
                                        <th scope="col" style="width: 10%">STATUS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($invoices as $no => $invoice)
                                        <tr>
                                            <th scope="row" style="text-align: center">
                                                {{ ++$no + ($invoices->currentPage() - 1) * $invoices->perPage() }}
                                            </th>
                                            <td>
                                                {{ $invoice->invoice }}
                                            </td>
                                            <td>{{ $invoice->name }}</td>
                                            <td>{{ moneyFormat($invoice->grand_total) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.order.show', $invoice->id) }}">
                                                    @if ($invoice->status == 'pending')
                                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase cursor-pointer p-2"
                                                            style="background: #2D5C7F;font-size:.7rem;">
                                                            {{ $invoice->status }}
                                                        </span>
                                                    @endif
                                                    @if ($invoice->status == 'success')
                                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase cursor-pointer p-2"
                                                            style="background: #105652;font-size:.7rem;">
                                                            {{ $invoice->status }}
                                                        </span>
                                                    @endif
                                                    @if ($invoice->status == 'expired')
                                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase cursor-pointer p-2"
                                                            style="background:#FECD51;font-size:.7rem;">
                                                            {{ $invoice->status }}
                                                        </span>
                                                    @endif
                                                    @if ($invoice->status == 'failed')
                                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase cursor-pointer p-2"
                                                            style="background: #911F27;font-size:.7rem;">
                                                            {{ $invoice->status }}
                                                        </span>
                                                    @endif
                                                </a>
                                            </td>
                                        </tr>

                                    @empty
                                        <div class="alert alert-danger">
                                            Data Belum Tersedia!
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>

                            <div style="text-align: center">
                                {{ $invoices->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection

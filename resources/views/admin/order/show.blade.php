@extends('layouts.app', ['title' => 'Detail Order'])

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid mb-5">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart"></i> DETAIL ORDER</h6>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td style="width: 25%">NO. INVOICE</td>
                                <td style="width: 1%">:</td>
                                <td>{{ $invoice->invoice }}</td>
                            </tr>
                            <tr>
                                <td>NAMA LENGKAP</td>
                                <td>:</td>
                                <td>{{ $invoice->name }}</td>
                            </tr>
                            <tr>
                                <td>NO. TELP / WA</td>
                                <td>:</td>
                                <td>{{ $invoice->phone }}</td>
                            </tr>
                            <tr>
                                <td>ALAMAT LENGKAP</td>
                                <td>:</td>
                                <td>{{ $invoice->address }}</td>
                            </tr>
                            <tr>
                                <td>TOTAL PEMBAYARAN</td>
                                <td>:</td>
                                <td>{{ moneyFormat($invoice->grand_total) }}</td>
                            </tr>
                            <tr>
                                <td>STATUS</td>
                                <td>:</td>
                                <td>
                                    @if ($invoice->status == 'pending')
                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase p-2" style="background: #2D5C7F;font-size:.7rem;">
                                            {{ $invoice->status }}
                                        </span>
                                    @endif
                                    @if ($invoice->status == 'success')
                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase p-2" style="background: #105652;font-size:.7rem;">
                                            {{ $invoice->status }}
                                        </span>
                                    @endif
                                    @if ($invoice->status == 'expired')
                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase p-2" style="background:#FECD51;font-size:.7rem;">
                                            {{ $invoice->status }}
                                        </span>
                                    @endif
                                    @if ($invoice->status == 'failed')
                                        <span class="badge badge-pill text-white font-weight-bolder text-uppercase p-2" style="background: #911F27;font-size:.7rem;">
                                            {{ $invoice->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    @if ($invoice->status == 'success')
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 rounded shadow">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold"><i class="fa fa-truck"></i> PENGIRIMAN</h6>
                                </div>

                                <div class="card-body">
                                    <div>
                                        <h2 class="font-weight-bolder text-uppercase">{{ $invoice->courier }}</h2>
                                        <h6>Service <strong>{{ $invoice->service }}</strong></h6>
                                        <h6>Biaya {{ moneyFormat($invoice->cost_courier) }}</h6>
                                        <form action="{{ route('admin.order.update', $invoice->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <input type="text" name="airway_bill" class="form-control @error('airway_bill') is-invalid @enderror"
                                                            value="{{ old('airway_bill', $invoice->airway_bill) }}" placeholder="Resi Pengiriman" required>

                                                        @error('airway_bill')
                                                            <div class="invalid-feedback" style="display: block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <button class="btn btn-green btn-submit font-weight-bolder" type="submit">UPDATE</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md">
                        <div class="card border-0 rounded shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold"><i class="fa fa-shopping-cart"></i> DETAIL ITEM</h6>
                            </div>

                            <div class="card-body">
                                @foreach ($invoice->orders()->get() as $product)
                                    <div class="row pb-3">
                                        <div class="col-4">
                                            <div class="wrapper-image-cart">
                                                <img src="{{ $product->image }}" style="width: 100%;border-radius: .5rem" />
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h5>
                                                <b style="font-size: 1.1rem">{{ $product->product_name }}</b>
                                            </h5>
                                            <p class="m-0" style="font-size: 1rem">
                                                Rp. {{ moneyFormat($product->price) }}
                                            </p>
                                            <p class="m-0" style="font-size: 1rem">
                                                Jumlah : {{ $product->qty }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="row">
                                    <div class="col">
                                        <p class="alert alert-success "><strong>Catatan Pesanan :</strong> {{ $invoice->note }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection

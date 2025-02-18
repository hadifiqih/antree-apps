@extends('layouts.app')

@section('title', 'Unduh Laporan Workshop')

@section('username', Auth::user()->name)

@section('page', 'Report')

@section('breadcrumb', 'Laporan Workshop')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Laporan Workshop</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('laporan-workshop-pdf') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{-- Pilih tempat workshop --}}
                                    <label for="tempat_workshop">Pilih Tempat Workshop</label>
                                    <select class="form-control" id="tempat_workshop" name="tempat_workshop" required>
                                        <option value="" selected disabled>Pilih Tempat Workshop</option>
                                        <option value="Surabaya">Surabaya</option>
                                        <option value="Malang">Malang</option>
                                        <option value="Kediri">Kediri</option>
                                        <option value="Sidoarjo">Sidoarjo</option>
                                        <option value="Gresik">Gresik</option>
                                        <option value="Pasar Kupang">Pasar Kupang</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- Button Submit --}}
                        <button type="submit" class="btn btn-primary">Unduh Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')


@endsection

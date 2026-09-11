@extends('dashboard.template')
@section('title', 'Dashboard')

@include('dashboard.js.log')

@section('content')
          <!-- / .main-navbar -->
          <div class="main-content-container container-fluid px-4">
            <!-- Page Header -->
            <div class="page-header row no-gutters py-4">
              <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
                <span class="text-uppercase page-subtitle">Dashboard</span>
                <h3 class="page-title">History Login</h3>
              </div>
            </div>
            <!-- End Page Header -->
            <!-- Default Light Table -->
            <div class="row">
              <div class="col">
                <div class="card card-small mb-4">
                  <div class="card-header border-bottom">
                    <h6 class="m-0">Active Users</h6>
                  </div>
                  <div class="card-body p-0 pb-3 text-center">
                   <table id="table-1" class="table table-striped" style="width:100%">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">IP Address</th>
              <th scope="col">Login at</th>
              <th scope="col">Login Successfully</th>
              <th scope="col">Logout at</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($logs as $key => $item)
          <tr>
              <th scope="row">{{ ++$key }}</th>
              <td>{{ $item['ip_address'] }}</td>
              <td>{{ Carbon\Carbon::parse($item['login_at'])->isoFormat('D MMMM YYYY h:mm A') }}</td>
              <td>{{ $item['login_successful'] ? 'Yes' : 'No' }}</td>
              <td>{{ $item['logout_at'] ? Carbon\Carbon::parse($item->logout_at)->isoFormat('D MMMM YYYY h:mm A') : '-' }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
            <tr>
              <th scope="col">#</th>
              <th scope="col">IP Address</th>
              <th scope="col">Login at</th>
              <th scope="col">Login Successfully</th>
              <th scope="col">Logout at</th>
            </tr>
        </tfoot>
    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

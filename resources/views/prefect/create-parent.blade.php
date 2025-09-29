@extends('prefect.layout')

@section('content')
<div class="main-container">

    {{-- ✅ Flash Messages --}}
    @if(session('messages'))
        <div class="alert-messages">
            @foreach(session('messages') as $msg)
                <div class="alert-item">{!! $msg !!}</div>
            @endforeach
        </div>
    @endif

  <div class="toolbar">
    <h2>Create Parents</h2>
    <div class="actions">
<input type="search" placeholder="🔍 Search parent..." id="searchInput">
      {{-- <button class="btn-primary" id="createBtn">➕ Add Student</button>
      <button class="btn-info" id="archiveBtn">🗃️ Archive</button> --}}

         <!-- ======= ACTION BUTTONS ======= -->
    <form id="violationForm" method="POST" action="{{ route('parent.store') }}">
        @csrf
        <div class="buttons-row">
            <button type="button" class="btn-Add-Violation" id="btnAddViolation">
                <i class="fas fa-plus-circle"></i> Add Another Parent
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save All
            </button>
        </div>
    </form>

    </div>
  </div>
</div>


@endsection

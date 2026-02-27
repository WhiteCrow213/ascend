
@if(session('success'))
<div id="toastSuccess" style="
    position:fixed;
    top:20px;
    right:20px;
    background:#5B4CE6;
    color:#fff;
    padding:14px 18px;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
    font-weight:800;
    z-index:9999;
    opacity:0;
    transform:translateY(-10px);
    transition:all .3s ease;
">
    {{ session('success') }}
</div>

<script>
    const toast = document.getElementById('toastSuccess');
    setTimeout(() => {
        toast.style.opacity = 1;
        toast.style.transform = 'translateY(0)';
    }, 100);

    setTimeout(() => {
        toast.style.opacity = 0;
        toast.style.transform = 'translateY(-10px)';
    }, 3000);
</script>
@endif

@extends('layouts.app')

@section('title', 'Programs')

@push('styles')
<style>
/* ================================
   ASCEND Programs Page (Purple)
================================ */

.asc-wrap { padding:16px; }

.asc-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-end;
  margin-bottom:16px;
  gap:12px;
}
.asc-head h1{ margin:0; }
.asc-sub{
  margin-top:6px;
  color:#6b7280;
  font-weight:600;
}

.asc-btn{
  padding:10px 14px;
  border-radius:14px;
  font-weight:800;
  border:none;
  cursor:pointer;
  transition: all .15s ease;
}

.asc-btn-primary{
  background: linear-gradient(135deg,#5B4CE6 0%, #7A5FFF 100%);
  color:#fff;
  box-shadow: 0 8px 18px rgba(91,76,230,.35);
}
.asc-btn-primary:hover{
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(91,76,230,.45);
}

.asc-btn-secondary{
  background:#fff;
  color:#4b3fd1;
  border:1px solid rgba(91,76,230,.25);
}
.asc-btn-secondary:hover{
  background: rgba(91,76,230,.08);
  transform: translateY(-1px);
}

.asc-card{
  background:#fff;
  border-radius:18px;
  box-shadow: 0 12px 28px rgba(20,20,40,.08);
  overflow:hidden;
}

.asc-table{
  width:100%;
  border-collapse:collapse;
}

.asc-table thead th{
  text-align:left;
  padding:14px;
  font-size:12px;
  font-weight:900;
  letter-spacing:.5px;
  color:#4b3fd1;
  background: linear-gradient(135deg, rgba(91,76,230,.12), rgba(122,95,255,.08));
  border-bottom:1px solid rgba(0,0,0,.06);
}

.asc-table tbody td{
  padding:14px;
  font-weight:700;
  color:#1e2142;
  border-bottom:1px solid rgba(0,0,0,.05);
}

.asc-table tbody tr:hover td{
  background: rgba(91,76,230,.06);
}

.asc-pill{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:900;
  background: rgba(91,76,230,.12);
  color:#4b3fd1;
}

.asc-dot{
  width:8px;
  height:8px;
  border-radius:999px;
  background:#5B4CE6;
}

.asc-row-actions button{
  padding:6px 10px;
  border-radius:10px;
  border:1px solid rgba(91,76,230,.25);
  background:#fff;
  font-weight:800;
  cursor:pointer;
  transition:.15s;
}
.asc-row-actions button:hover{
  background: rgba(91,76,230,.08);
  transform: translateY(-1px);
}
</style>
@endpush

@section('content')
<div class="asc-wrap">

  <div class="asc-head">
    <div>
      <h1>Programs</h1>
      <p class="asc-sub">Manage academic programs.</p>

      <a href="{{ route('utilities.master-data') }}" class="asc-btn asc-btn-secondary" style="display:inline-flex;align-items:center;gap:8px;margin-top:12px;">
        <span style="font-weight:900;">←</span> Back
      </a>
    </div>

    <div style="display:flex;align-items:center;gap:12px;">
      <button class="asc-btn asc-btn-primary" type="button" onclick="openAddModal()">
        + Add Program
      </button>
    </div>
  </div>

  <div class="asc-card">
    <table class="asc-table">
      <thead>
        <tr>
          <th style="width:160px;">Code</th>
          <th>Name</th>
          <th style="width:200px;">College</th>
          <th style="width:120px;">Status</th>
          <th style="width:120px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($programs as $program)
        <tr>
          <td>{{ $program->program_code }}</td>
          <td>{{ $program->program_name }}</td>
          <td>{{ $program->college_name ?? '—' }}</td>
          <td>
            <span class="asc-pill">
              <span class="asc-dot"></span>
              {{ $program->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            <div class="asc-row-actions">
              <button type="button"
                onclick='openEditModal(
                  {{ $program->IDProgram }},
                  @json($program->program_code),
                  @json($program->program_name),
                  @json($program->collegeID),
                  @json($program->IDcurr)
                )'>
                Edit
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" style="padding:20px;color:#6b7280;">
            No programs found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

@include('utilities.programs.modal')
@endsection

@extends('layouts.app')

@section('title', 'Dean Module')

@push('styles')
<style>
  .dean-hub { padding: 16px; }

  .dean-hub p {
    margin: 0 0 14px;
    color: #6b7280;
  }

  .dean-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 18px;
  }

  .dean-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(18, 24, 40, 0.06);
    text-decoration: none;
    color: inherit;
    transition: transform .08s ease, box-shadow .08s ease;
  }

  .dean-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(18, 24, 40, 0.10);
  }

  .dean-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(124, 58, 237, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .dean-card p {
    margin: 0;
  }

  .dean-title {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.15;
  }

  .dean-sub {
    color: #6b7280;
    font-weight: 600;
    font-size: 14px;
    margin-top: 2px;
  }

  @media (max-width: 900px) {
    .dean-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
<div class="dean-hub">

  <h1>Dean Module</h1>
  <p>Manage academic operations, curriculum, faculty, and student monitoring.</p>

  <div class="dean-grid">

    <a class="dean-card" href="{{ route('dean.sections-offerings') }}">
      <div class="dean-icon">🏫</div>
      <div>
        <p class="dean-title">Sections & Offerings</p>
        <p class="dean-sub">Create sections and set subject schedules, rooms, and seat limits</p>
      </div>
    </a>


    <a class="dean-card" href="#">
      <div class="dean-icon">🎓</div>
      <div>
        <p class="dean-title">Student Records</p>
        <p class="dean-sub">View and monitor student academic information</p>
      </div>
    </a>

    <a class="dean-card" href="#">
      <div class="dean-icon">🧭</div>
      <div>
        <p class="dean-title">Curriculum Map</p>
        <p class="dean-sub">Review and manage program curriculum structures</p>
      </div>
    </a>

    <a class="dean-card" href="#">
      <div class="dean-icon">👩‍🏫</div>
      <div>
        <p class="dean-title">Faculty Data</p>
        <p class="dean-sub">View faculty profiles, teaching assignments, and loads</p>
      </div>
    </a>

    <a class="dean-card" href="#">
      <div class="dean-icon">⭐</div>
      <div>
        <p class="dean-title">Faculty Evaluation</p>
        <p class="dean-sub">Review teaching evaluations and performance metrics</p>
      </div>
    </a>

  </div>

</div>
@endsection

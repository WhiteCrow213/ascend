@extends('layouts.app')

@section('title', 'Master Data')

@push('styles')
<style>
  .md-wrap { padding: 16px; }

  .md-wrap p {
    margin: 0 0 14px;
    color: #6b7280;
  }

  .md-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 18px;
  }

  .md-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(18, 24, 40, 0.06);
    text-decoration: none;
    color: inherit;
    transition: transform .08s ease, box-shadow .08s ease;
  }

  .md-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(18, 24, 40, 0.10);
  }

  .md-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(124, 58, 237, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .md-title {
    font-size: 18px;
    font-weight: 800;
    margin: 0;
    line-height: 1.1;
  }

  .md-sub {
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    margin-top: 2px;
  }

  @media (max-width: 900px) {
    .md-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
<div class="md-wrap">

  <h1>Master Data</h1>
  <p>Configure academic structure and system master records.</p>

  <div class="md-grid">

    <a class="md-card" href="{{ route('utilities.programs.index') }}">
      <div class="md-icon">🎓</div>
      <div>
        <p class="md-title">Programs</p>
        <p class="md-sub">Manage academic programs and courses</p>
      </div>
    </a>

    <a class="md-card" href="{{ route('utilities.subjects.index') }}">
      <div class="md-icon">📘</div>
      <div>
        <p class="md-title">Subjects</p>
        <p class="md-sub">Create and manage subject catalog</p>
      </div>
    </a>

    <a class="md-card" href="{{ route('utilities.curriculum.index') }}">
      <div class="md-icon">🗂️</div>
      <div>
        <p class="md-title">Curriculum</p>
        <p class="md-sub">Build curriculum and assign subjects</p>
      </div>
    </a>

    <a class="md-card" href="{{ route('utilities.curriculum.map.index') }}">
  <div class="md-icon">🧩</div>
  <div>
    <p class="md-title">Curriculum Map</p>
    <p class="md-sub">Map subjects by year level and semester</p>
  </div>
</a>

  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Utilities')

@push('styles')
<style>
  .util-hub { padding: 16px; }

  /* Removed custom h1 styling so it matches Admissions */

  .util-hub p {
    margin: 0 0 14px;
    color: #6b7280;
  }

  .util-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 18px;
  }

  .util-card {
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

  .util-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(18, 24, 40, 0.10);
  }

  .util-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: rgba(124, 58, 237, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .util-card p {
    margin: 0;
  }

  .util-title {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.15;
  }

  .util-sub {
    color: #6b7280;
    font-weight: 600;
    font-size: 14px;
    margin-top: 2px;
  }

  @media (max-width: 900px) {
    .util-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
<div class="util-hub">

  {{-- This now inherits the SAME styling as Admissions --}}
  <h1>Utilities</h1>
  <p>Choose a section to manage system configuration and master data.</p>

  <div class="util-grid">

    <a class="util-card" href="{{ url('/utilities/terms') }}">
      <div class="util-icon">🗓️</div>
      <div>
        <p class="util-title">School Year & Terms</p>
        <p class="util-sub">Manage school years, terms, and active term</p>
      </div>
    </a>

    <a class="util-card" href="{{ route('utilities.master-data') }}">
      <div class="util-icon">🧱</div>
      <div>
        <p class="util-title">Master Data</p>
        <p class="util-sub">Programs, subjects, curriculum, sections</p>
      </div>
    </a>

  </div>
</div>
@endsection

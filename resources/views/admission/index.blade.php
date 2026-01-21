@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
/* Admissions hub card tweaks */
.adm-grid{
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.adm-card{
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  padding: 18px;
  text-decoration: none;
  color: inherit;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  transition: box-shadow .2s ease, transform .2s ease;
}

.adm-card:hover{
  box-shadow: 0 10px 30px rgba(17,24,39,.08);
  transform: translateY(-2px);
}

.adm-icon{
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(118,0,188,.12);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}

/* emoji safely injected */
.adm-icon::before{
  content: attr(data-icon);
}

.adm-title{
  font-size: 18px;
  font-weight: 700;
  margin: 0;
}

.adm-desc{
  font-size: 14px;
  color: #6b7280;
  margin: 2px 0 0;
}

.adm-disabled{
  opacity: .55;
  pointer-events: none;
}
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">

  <h1 class="text-3xl font-bold mb-2">Admissions</h1>
  <p class="text-gray-600 mb-8">
    Choose a section to manage admissions workflows.
  </p>

  <div class="adm-grid">

    {{-- PRE-REGISTRATION --}}
    <a href="{{ route('admission.prereg.grid') }}" class="adm-card">
      <div class="adm-icon" data-icon="📝"></div>
      <div>
        <h2 class="adm-title">Pre-registration</h2>
        <p class="adm-desc">Applicants, exams, requirements</p>
      </div>
    </a>

    {{-- ENROLLMENT (disabled for now) --}}
    <div class="adm-card adm-disabled">
      <div class="adm-icon" data-icon="🎓"></div>
      <div>
        <h2 class="adm-title">Enrollment</h2>
        <p class="adm-desc">Subjects, assessment, COR</p>
      </div>
    </div>

  </div>
</div>
@endsection

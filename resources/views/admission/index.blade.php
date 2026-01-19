@extends('layouts.app')

@section('title', 'Admissions')

@section('content')
<style>
  .ad-wrap{
    min-height: calc(100vh - 120px);
    display:flex;
    align-items:center;
    justify-content:center;
  }

  .ad-panel{
    width: min(520px, 95%);
    background:#fff;
    border-radius:20px;
    padding:20px;
    border:1px solid rgba(0,0,0,.08);
    box-shadow:0 20px 50px rgba(0,0,0,.08);
  }

  .ad-title{
    font-size:20px;
    font-weight:800;
    margin:0 0 4px;
  }

  .ad-sub{
    font-size:13px;
    color:#6b7280;
    margin-bottom:16px;
  }

  .ad-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
  }

  .ad-box{
    text-decoration:none;
    border:1px solid rgba(0,0,0,.08);
    border-radius:16px;
    padding:16px;
    display:flex;
    gap:12px;
    align-items:center;
    color:#111;
  }

  .ad-box:hover{
    box-shadow:0 12px 30px rgba(0,0,0,.10);
    border-color:#7600bc;
  }

  .ad-icon{
    width:36px;
    height:36px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(118,0,188,.12);
    font-size:18px;
  }

  .ad-name{
    font-weight:800;
  }

  .ad-desc{
    font-size:12px;
    color:#6b7280;
  }

  @media(max-width:500px){
    .ad-grid{ grid-template-columns:1fr; }
  }
</style>

<div class="ad-wrap">
  <div class="ad-panel">
    <div class="ad-title">Admissions</div>
    <div class="ad-sub">Choose a section to manage admissions workflows.</div>

    <div class="ad-grid">
      <a class="ad-box" href="{{ route('admission.pre_registration.index') }}">
        <div class="ad-icon">📝</div>
        <div>
          <div class="ad-name">Pre-registration</div>
          <div class="ad-desc">Applicants, exams, requirements</div>
        </div>
      </a>

      <a class="ad-box" href="{{ route('admission.enrollment.index') }}">
        <div class="ad-icon">🎓</div>
        <div>
          <div class="ad-name">Enrollment</div>
          <div class="ad-desc">Subjects, assessment, COR</div>
        </div>
      </a>
    </div>
  </div>
</div>
@endsection

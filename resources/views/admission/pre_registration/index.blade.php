@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Pre-registration</h1>
      <p class="text-sm text-gray-500">Applicants list (placeholder for now).</p>
    </div>

    <a href="{{ route('admission.index') }}" class="text-sm text-purple-700 hover:underline">
      ← Back to Admissions
    </a>
  </div>

  <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="font-semibold">Applicants</div>
      <input type="text" placeholder="Search applicant..."
             class="border rounded-xl px-3 py-2 text-sm w-64">
    </div>

    <div class="p-4 text-sm text-gray-600">
      No data yet. Once your tables are connected, we’ll load records here.
    </div>
  </div>
</div>
@endsection

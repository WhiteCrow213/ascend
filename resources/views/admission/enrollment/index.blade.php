@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Enrollment</h1>
      <p class="text-sm text-gray-500">Enrollment management (placeholder for now).</p>
    </div>

    <a href="{{ route('admission.index') }}" class="text-sm text-purple-700 hover:underline">
      ← Back to Admissions
    </a>
  </div>

  <div class="bg-white border rounded-2xl shadow-sm p-4">
    No enrollment features yet. We’ll connect this to your enrollment tables next.
  </div>
</div>
@endsection

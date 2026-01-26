@props(['align' => 'between']) {{-- between | right | left --}}

@php
  $map = [
    'between' => 'actions actions-between',
    'right'   => 'actions actions-right',
    'left'    => 'actions actions-left',
  ];
@endphp

<div {{ $attributes->merge(['class' => $map[$align] ?? $map['between']]) }}>
  {{ $slot }}
</div>

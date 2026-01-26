@props([
  'variant' => 'primary', // primary | ghost | danger
  'type' => 'button',
])

@php
  $base = 'btn';
  $variants = [
    'primary' => 'btn-primary',
    'ghost'   => 'btn-ghost',
    'danger'  => 'btn-danger',
  ];
  $cls = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>
  {{ $slot }}
</button>

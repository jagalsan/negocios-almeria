@props(['movement' => 0, 'label' => 'Sin cambios'])

@if($movement > 0)
<span class="ranking-movement ranking-movement-up" aria-label="{{ $label }}"><span aria-hidden="true">↑</span> {{ $movement }}</span>
@elseif($movement < 0)
<span class="ranking-movement ranking-movement-down" aria-label="{{ $label }}"><span aria-hidden="true">↓</span> {{ abs($movement) }}</span>
@else
<span class="ranking-movement ranking-movement-stable">Sin cambios</span>
@endif

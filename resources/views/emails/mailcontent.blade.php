{{-- SPDX-License-Identifier: MIT --}}
@component('mail::message')

	{!! html_entity_decode($content) !!}

@endcomponent

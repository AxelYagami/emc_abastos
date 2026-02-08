@extends('layouts.storefront')

@section('content')
    @php
        // Get storefront template from empresa's template_config
        $storefrontTemplate = $currentStore?->template_config['storefront_template'] ?? 'classic';
        
        // Validate template exists, fallback to classic
        $validTemplates = ['classic', 'modern'];
        if (!in_array($storefrontTemplate, $validTemplates)) {
            $storefrontTemplate = 'classic';
        }
    @endphp

    {{-- Include the selected template --}}
    @include('store.templates.' . $storefrontTemplate . '.home')
@endsection

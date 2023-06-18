<option value="{{ $node['id'] }}" {{ $node['state']['disabled'] ? 'disabled' : '' }} data-parent="{{ $node['attr']['data-parent'] }}" data-icon="{{ $node['attr']['data-icon'] }}" data-new="{{ $node['attr']['data-new'] }}">{{ $node['text'] }}</option>

@if ($node['children'])
    @foreach ($node['children'] as $childNode)
        @include('partials.tree_option', ['node' => $childNode])
    @endforeach
@endif

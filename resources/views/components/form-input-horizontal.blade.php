@props([
    'label',
    'name',
    'placeholder',
    'type' => 'text',
    'value' => '',
    'class' => '',
    'styleRight' => '',
    'styleInput' => '',
])
<div class="row mb-3">
    <label class="col-sm-3 col-form-label">{{ $label }}</label>
    <div class="col-sm-9" style="{{ $styleRight }}">
        <input type="{{ $type }}" class="form-control {{ $class }}" name="{{ $name }}"
            placeholder="{{ $placeholder }}" value="{{ $value }}" style="{{ $styleInput }}" />
    </div>
</div>

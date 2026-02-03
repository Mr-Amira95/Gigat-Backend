@props(['selected' => null])

<select {{ $attributes }}>
    @foreach(App\Enums\FilterTypeEnum::cases() as $type)
        <option value="{{ $type->value }}" {{ $selected == $type->value ? 'selected' : '' }}>
            {{ $type->label() }}
        </option>
    @endforeach
</select> 
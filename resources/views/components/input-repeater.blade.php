@props([
    'name',
    'id',
    'label' => null,
    'value' => [],
    'oldInputKey' => null,
    'oldInputValue' => null,
])

@php
    $useSubmittedRows = $oldInputKey !== null
        && (string) old($oldInputKey) === (string) $oldInputValue;

    if ($useSubmittedRows) {
        $rows = old($name, []);
        $rows = is_array($rows) ? $rows : [];
    } else {
        $features = is_array($value) ? $value : [];
        $rows = [];

        foreach ($features as $key => $featureValue) {
            if (is_int($key)) {
                $rows[] = [
                    'key' => is_string($featureValue) ? $featureValue : '',
                    'value' => '',
                ];

                continue;
            }

            $rows[] = [
                'key' => (string) $key,
                'value' => is_bool($featureValue)
                    ? ($featureValue ? 'true' : 'false')
                    : (is_scalar($featureValue) ? (string) $featureValue : json_encode($featureValue)),
            ];
        }
    }

    $indexes = array_map('intval', array_keys($rows));
    $nextIndex = $indexes === [] ? 0 : max($indexes) + 1;
    $label = $label ?? Str::headline($name);
@endphp

<input type="hidden" name="{{ $name }}_present" value="1">
@if ($oldInputKey !== null)
    <input type="hidden" name="{{ $oldInputKey }}" value="{{ $oldInputValue }}">
@endif

<div class="col-12" data-key-value-editor data-next-index="{{ $nextIndex }}">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="form-label mb-0">{{ $label }}</span>
        <button class="btn btn-sm btn-outline-primary" type="button" data-key-value-add>
            <i class="fa-solid fa-plus me-1"></i>@lang('Add row')
        </button>
    </div>

    <div class="d-grid gap-2" data-key-value-rows>
        @foreach ($rows as $rowIndex => $row)
            @php
                $rowKey = is_array($row) && is_scalar($row['key'] ?? null) ? (string) $row['key'] : '';
                $rowValue = is_array($row) ? ($row['value'] ?? '') : '';
                $rowValue = is_bool($rowValue)
                    ? ($rowValue ? 'true' : 'false')
                    : (is_scalar($rowValue) ? (string) $rowValue : '');
            @endphp
            <div class="row g-2 align-items-start" data-key-value-row>
                <div class="col-md-5">
                    <input class="form-control @error($name.'.'.$rowIndex.'.key') is-invalid @enderror"
                        type="text"
                        id="{{ $id }}-{{ $rowIndex }}-key"
                        name="{{ $name }}[{{ $rowIndex }}][key]"
                        value="{{ $rowKey }}"
                        maxlength="255"
                        placeholder="@lang('Key')"
                        aria-label="@lang('Key')"
                        required>
                    @error($name.'.'.$rowIndex.'.key')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <input class="form-control @error($name.'.'.$rowIndex.'.value') is-invalid @enderror"
                        type="text"
                        id="{{ $id }}-{{ $rowIndex }}-value"
                        name="{{ $name }}[{{ $rowIndex }}][value]"
                        value="{{ $rowValue }}"
                        maxlength="255"
                        placeholder="@lang('Value')"
                        aria-label="@lang('Value')"
                        required>
                    @error($name.'.'.$rowIndex.'.value')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-danger w-100" type="button" data-key-value-remove aria-label="@lang('Remove row')">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <template data-key-value-template>
        <div class="row g-2 align-items-start" data-key-value-row>
            <div class="col-md-5">
                <input class="form-control" type="text" id="{{ $id }}-__INDEX__-key"
                    name="{{ $name }}[__INDEX__][key]" maxlength="255"
                    placeholder="@lang('Key')" aria-label="@lang('Key')" required>
            </div>
            <div class="col-md-5">
                <input class="form-control" type="text" id="{{ $id }}-__INDEX__-value"
                    name="{{ $name }}[__INDEX__][value]" maxlength="255"
                    placeholder="@lang('Value')" aria-label="@lang('Value')" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-danger w-100" type="button" data-key-value-remove aria-label="@lang('Remove row')">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    </template>
</div>

@once
    <script>
        document.addEventListener('click', (event) => {
            const addButton = event.target.closest('[data-key-value-add]');

            if (addButton) {
                const editor = addButton.closest('[data-key-value-editor]');
                const template = editor.querySelector('[data-key-value-template]');
                const index = Number(editor.dataset.nextIndex);
                const row = template.content.cloneNode(true);

                editor.dataset.nextIndex = String(index + 1);
                row.querySelectorAll('[name], [id]').forEach((input) => {
                    input.name = input.name?.replace(/__INDEX__/g, String(index));
                    input.id = input.id?.replace(/__INDEX__/g, String(index));
                });
                editor.querySelector('[data-key-value-rows]').append(row);

                return;
            }

            const removeButton = event.target.closest('[data-key-value-remove]');
            removeButton?.closest('[data-key-value-row]')?.remove();
        });
    </script>
@endonce

<div class="col-sm">
    <form id="{{ $formId }}" onsubmit="return submitThisForm(this, '{{ $target }}')">
        {{ csrf_field() }}
            @foreach($fields as $index=> $field)
            <div class="form-group">
                <input type="hidden" name="type_id_{{ $index }}" value="{{ $field->id }}"/>
                <label for="field_{{$formId}}_{{ $index }}">{{ $field->type_name }}</label>
                <input
                    id="field_{{$formId}}_{{ $index }}"
                    name="field_{{ $index }}"
                    class="form-control"
                    type="number"
                    min="{{ $field->min }}"
                    max="{{ $field->max }}"
                    step="<?=pow(10, (0 - $field->decimals))?>"
                    required
                />
            </div>
        @endforeach
        <input type="submit" value="Save" id="submit_{{ $formId }}" class="btn btn-primary float-right"/>
    </form>
</div>

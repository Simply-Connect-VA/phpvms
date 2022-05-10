@if($settings)
  <div style="margin-bottom: 5px;">
    {{ Form::open(array('route' => 'DisposableTools.dtsettings', 'method' => 'post')) }}
      <table class="table table-borderless table-stiped text-left" style="margin-bottom: 5px;">
        @foreach($settings->where('group', $group)->sortBy('order') as $st)
          <tr>
            <td style="width:35%; max-width: 35%;">{{ $st->name }}</td>
            <td>
              @if($st->field_type === 'check')
                <input type="hidden" name="{{ $st->id }}" value="false">
                <input class="form-control" type="checkbox" name="{{ $st->id }}" value="true" @if($st->value === 'true') checked @endif>
              @elseif($st->field_type === 'select')
                @php $values = explode(',', $st->options); @endphp
                <select class="form-control" name="{{ $st->id }}">
                  @foreach($values as $value)
                    <option value="{{ $value }}" @if($st->value === $value || !filled($st->value) && $st->default === $value) selected @endif>{{ $value }}</option>
                  @endforeach
              @else
                <input
                  class="form-control"
                  @if($st->field_type === 'decimal')
                    type="number" step="0.0001" min="0" max="9999"
                  @elseif($st->field_type === 'numeric')
                    type="number" step="1" min="0" max="9999"
                  @else
                    type="text" maxlength="500"
                  @endif
                  name="{{ $st->id }}" placeholder="{{ $st->default }}" value="{{ $st->value }}">
              @endif
            </td>
          </tr>
        @endforeach
      </table>
      <input type="hidden" name="group" value="{{ $group }}">
      <input class="button" type="submit" value="Save Section Settings">
    {{ Form::close() }}
  </div>
  <style>
    ::placeholder { color: indianred !important; opacity: 0.5 !important; }
    :-ms-input-placeholder { color: indianred !important; }
    ::-ms-input-placeholder { color: indianred !important; }
  </style>
@endif
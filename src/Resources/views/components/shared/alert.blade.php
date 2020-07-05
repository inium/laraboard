{{--
  -- alert 템플릿
  --
  -- 사용방법
  -- ---------------------------------------------------------------------------
  -- @include('laraboard::components.shared.alert', [
  --        'class' => Session::get('alert-class', 'alert-info'),
  --        'message' => Session::get('message')
  --    ])
  -- ---------------------------------------------------------------------------
  --
  -- @param string class    Alert class
  -- @param string message  Alert message
  --}}

<div class="alert {{ $class }}" role="alert">
    {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

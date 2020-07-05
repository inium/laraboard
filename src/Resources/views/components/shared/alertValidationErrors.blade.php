{{--
  -- alert Form Validation 오류 목록 템플릿
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
<div class="alert alert-danger alert-dismissible fade show">
    <div>
    <ul class="pl-2 mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

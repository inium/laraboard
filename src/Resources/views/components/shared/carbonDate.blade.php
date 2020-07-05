{{--
  -- Carbon Date의 locale을 한국(ko)로 바꾼 후 출력
  --
  -- 사용방법
  -- ---------------------------------------------------------------------------
  -- @include ('laraboard::component.shared.carbonDate', [
  --        'date' => $date
  --    ])
  -- ---------------------------------------------------------------------------
  -- @param Carbon date     Carbon Date
  --}}

<span>
    {{-- {{ $date->setTimezone('Asia/Seoul')->diffForHumans() }} --}}
    {{-- {{ $date->locale(config('app.locale'))->diffForHumans() }} --}}
    {{ $date->locale('ko')->diffForHumans() }}
</span>

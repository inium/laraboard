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
    {{ $date->locale('ko')->diffForHumans() }}
</span>

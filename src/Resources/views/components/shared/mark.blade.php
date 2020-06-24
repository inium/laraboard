{{--
  -- 게시글 mark (강조)
  --
  -- 사용방법
  -- ---------------------------------------------------------------------------
  -- @include('laraboard::components.shared.mark', [
  --        'query' => $query,
  --        'content' => $subject
  --    ])
  -- ---------------------------------------------------------------------------
  -- @param string query        mark 대상 단어
  -- @param string content      mark 대상이 존재하는 문자열
  --}}
<span>
    {!!
        str_replace($query,
                    "<mark>{$query}</mark>",
                    htmlspecialchars_decode($content))
    !!}
</span>

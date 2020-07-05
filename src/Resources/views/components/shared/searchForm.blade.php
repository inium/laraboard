{{-- 
  -- 검색 Form
  --
  -- 사용방법
  -- ---------------------------------------------------------------------------
  -- @include ('laraboard::components.searchForm', [
  --        'action' => route('board.posts.view', [
                                'boardName' => $board['name']
                                ]),
  --        'query' => $query
  --        ])
  -- ---------------------------------------------------------------------------
  -- @param string action   Form Action URL
  -- @param string query    검색어
  --}}

<form action="{{ $action }}" method="GET" class="form-inline">

    <div class="form-input mr-2">
        <input type="text" name="query" class="form-control"
                value="{{ $query }}"
                placeholder="검색어 입력">
    </div>

    <div class="form-group">
        <button class="btn btn-primary" type="submit">
            검색
        </button>
    </div>

</form>

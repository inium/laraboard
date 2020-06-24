{{--
  -- 사용자 썸네일 출력
  -- 썸네일이 없을 경우 아래 이미지 색상을 수정한 후 생성한 base64 문자열 적용
  -- ---------------------------------------------------------------------------
  --    - iconfinder.com의 free 라이센스 이미지 이용
  --    - https://www.iconfinder.com/editor/?id=4696674&hash=ad29bd681ae51b0e195aaf30adea49dea2d85ba48ad3fc0ead273777
  -- ---------------------------------------------------------------------------
  -- 
  -- 사용방법
  -- ---------------------------------------------------------------------------
  -- @include ('laraboard::components.shared.thumbnail', [
            'thumbnail' => $thumbnail,
            'alt' => 'thumbnail',
            'class' => 'rounded-circle thumbnail align-self-start mr-1'
            ])
  -- ---------------------------------------------------------------------------
  -- @param string thumbnail    (optional) 썸네일 저장 경로
  -- @param string alt          img 태그의 alt
  -- @param string class        img 태그에 적용할 class
  --}}

@php

    $thumbnailPath = null;

    if (isset($thumbnail)) {
        $thumbnailPath = $thumbnail;
    }
    else {
        $thumbnailPath = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAAB0CAMAAABjROYVAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAANhQTFRF5eXl4+Pj29vb1dXV0tLSzs7O2dnZxcXFsrKypaWlmpqamZmZ2NjYvb29oqKi5OTkzc3Nqamppqamp6en2trarq6ur6+v4eHh3d3doKCgpKSko6Oj3t7eqKioubm5zMzM4ODgn5+fuLi419fXt7e3nZ2dnp6es7Ozx8fHu7u7vLy8sLCwoaGhnJycrKysxMTEw8PDm5ubysrKy8vL1tbWurq60NDQxsbGtLS0vr6+1NTUwMDAwsLC4uLi09PTtbW10dHRtra2z8/P39/fq6urqqqq3NzcwcHBrUoEAgAAA5FJREFUeJztmmlX2kAUhhMMWwZJQCsgKCCLVoGCG1GMtNrl//+jhmM5RZj1vZN+6PH5zPE5ZmZu7rw3jvPBBx/8p7iZPS+b9fYy7j8S5vKFos/+4BcL+VzaxtJ+me1Q3i+lZwzCyq7xjUoYpOMMqyLlioMwBeXhJ5lyxdGhbWfeVzmTXZW3qqwV1MoVhZo9Z72o52SsWrflzHGOiYiypVObaeg7GWtkbDjrxyZOxpotutM9MXMmhYJekE9NnYydUp1tcydjbZqz00WkjQ5JeoY4GTujOHuYk7EeQapZ/XYp4M4c6mQML0x9XAqvqjvApUO0QkBndA16Vs8p0nNQavR22aaBOUsUJ7p/L2jSC0j6mSa9hKRXNOkVJJX21mqqkHREk44g6ZAmHUBSjZ5ehg9JxzTpGJKSChK6pk2a9BiSTmjSCSQlvMJXYK/xLzTpFJJ6NKkHSd0ZxTkD+xXSTsL2keNMKVJsSR2nRSiEPpwDHOHSI9TpZHFpFpYGB6izSkjtrlHpDe50AmECKadCiidvoQ3s31KcYB96R3MCiQ5jJ+RMJ2Pcnw0tZGb3hnV/dk93Gp+baxtOx5mbOOd2nMmVXPvg+MSwbBNPsx1tYO2CgJbWC31iLdd+I5gqo5bB1P5sphXJnZGFcJlDLxJuKD9Kb/KVeeDeNZoPVoL7Lf4uVvC4mLz7f/3J4jHg/JCKO+32NxcsyIVPl/0o6l8+hblNTeus+2RponqzeqSDRaz6XbxYbfCyjTL4vG4eRnPpTKszX2cUFWqFcDfz9PFS+Oe+Ljfv7d+UT0XG4XY7WN5/ed3+0evLzvi4SojTr7lVqLnMt71SPY7rJa+dX3IP0RgL6ZJHS7oVR9BUMyZe/yuAtfad5kz6s53VV9H5QXUm28nQWgNb+y2r0fs1tuJkrGiyrks7TqNrqlH7J2eh69wjxSrv8V/0nK8Gw341mpcMQtLAQ+uuGtp1avX8LjFx3aXbUUoXtp0aeehPYojOw1e9XUmTRBGKnJAwkJYh75ru0pFK5/IxYSAtYya751gsuu95kEjhLFDFSFyWHtNyysJC4phWhngun9rTTWqh6Pm20nMyJsoo4XRXh18CqbXOiIeoFBLn0XJ8/k2unqaTsWeu9D5dKb+BIE4RVfDjbvAjNl34fTf8QZkeJ1yphXuaDP5g3vBDU1P43z0oP8qm0eRKe9AnmLqMBfOEWradGuFGw/Ibgb5JQLrWUYAAAAAASUVORK5CYII=';
    }

@endphp

<img src=" {{$thumbnailPath}} " alt="{{ $alt }}" class="{{ $class }}">

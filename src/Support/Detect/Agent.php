<?php

namespace Inium\Laraboard\Support\Detect;

class Agent
{
    /**
     * User Agent를 분석한 결과를 반환한다.
     *
     * @param string $agent     User Agent String
     * @return object           분석결과. 아래의 항목들을 반환.
     *                          - agent: User Agent String
     *                          - device_type: 사용자 접속기기 형태.
     *                                  desktop, tablet, mobile, others 중 1.
     *                          - os_name: 사용자 접속 운영체제 이름.
     *                                     없거나 분석 불가할 경우 null.
     *                          - os_version: 사용자 접속 운영체제 버전.
     *                                        없거나 분석 불가할 경우 null.
     *                          - browser_name: 사용자 접속 브라우저 이름.
     *                                          없거나 분석 불가할 경우 null.
     *                          - browser_version: 사용자 접속 브라우저 버전.
     *                                          없거나 분석 불가할 경우 null.
     */
    public static function parse(string $agent = null): object
    {
        if (is_null($agent)) {
            return (object) [
                "agent" => null,
                "device_type" => null,
                "os_name" => null,
                "os_version" => null,
                "browser_name" => null,
                "browser_version" => null,
            ];
        }

        $ua = new \Jenssegers\Agent\Agent();
        $ua->setUserAgent($agent);

        $platform = $ua->platform();
        $browser = $ua->browser();

        // User Agent로부터 사용자의 접속 기기 형태를 분석한다.
        $deviceType = "others";
        if ($ua->isDesktop()) {
            $deviceType = "desktop";
        } elseif ($ua->isTablet()) {
            $deviceType = "tablet";
        } elseif ($ua->isMobile()) {
            $deviceType = "mobile";
        } else {
            $deviceType = "others";
        }

        return (object) [
            "agent" => $agent,
            "device_type" => $deviceType,
            "os_name" => $platform ?: null,
            "os_version" => $ua->version($platform) ?: null,
            "browser_name" => $browser ?: null,
            "browser_version" => $ua->version($browser) ?: null,
        ];
    }
}

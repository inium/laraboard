<?php
/**
 * User Agent Wrapper Class
 *
 * Jenssengers/agent wrapper
 *
 * @see https://github.com/jenssegers/agent
 */

namespace Inium\Laraboard\Library;

class Agent extends \Jenssegers\Agent\Agent
{
    /**
     * User Agent를 분석한 결과를 반환한다.
     *
     * @param string $agent     User Agent String
     * @return array            분석결과. 아래의 항목들을 배열로 반환.
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
    public function parse(string $agent): array
    {
        $this->setUserAgent($agent);

        $platform = $this->platform();
        $browser = $this->browser();

        // User Agent로부터 사용자의 접속 기기 형태를 분석한다.
        $deviceType = 'others';
        if      ($this->isDesktop())    {   $deviceType = 'desktop';    }
        else if ($this->isTablet())     {   $deviceType = 'tablet';     }
        else if ($this->isMobile())     {   $deviceType = 'mobile';     }
        else                            {   $deviceType = 'others';     }

        return [
            'agent' => $agent,
            'device_type' => $deviceType,
            'os_name' => $platform ?: null,
            'os_version' => $this->version($platform) ?: null,
            'browser_name' => $browser ?: null,
            'browser_version' => $this->version($browser) ?: null
        ];
    }
}

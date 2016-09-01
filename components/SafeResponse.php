<?php

/**
 * ###############################################################################
 * @author Pawel Brzozowski <pb@human-device.com>
 * @copyright Copyright (c) 2016, Human Device Sp. z o.o. https://human-device.com
 * ###############################################################################
 */
 
namespace common\components\web;

use yii\web\HeaderCollection;
use yii\web\Response;

/**
 * Modified response for additional safety headers.
 * Configure the options below to suit your needs.
 * 
 * Replace the default Response component with this one in configuration:
 *  'components' => [
 *      'response' => [
 *          'class' => 'common\components\web\SafeResponse',
 *          // configuration here
 *      ],
 *  ]
 * Remember to set safe Cookies in configuration as well i.e.:
 *  'components' => [
 *      'user' => [
 *          // configuration here
 *          'identityCookie' => [
 *              'name' => '_identity',
 *              'httpOnly' => true,
 *              'secure' => true,
 *          ],
 *      ],
 *      'request' => [
 *          // configuration here
 *          'csrfCookie' => [
 *              'httpOnly' => true,
 *              'secure' => true,
 *          ],
 *      ],
 *  ]
 */
class SafeResponse extends Response
{
    /**
     * @var int Security level to be set for X-Frame-Options.
     * 3 = DENY
     * 2 = SAMEORIGIN
     * 1 = ALLOW-FROM $frameAllowFrom
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-Frame-Options
     */
    public $frameLevel = 3;
    /**
     * @var string URI for X-Frame-Options ALLOW-FROM.
     */
    public $frameAllowFrom = '';
    /**
     * @var int Security level to be set for X-XSS-Protection.
     * 3 = filter enabled with report=$xssReport
     * 2 = filter enabled with mode=block
     * 1 = filter enabled
     * 0 = filter disabled
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-XSS-Protection
     */
    public $xssLevel = 2;
    /**
     * @var string URI for X-XSS-Protection report.
     */
    public $xssReport = '';
    /**
     * @var int Number of seconds browser should remember that this site is only to be accessed using HTTPS.
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#HTTP_Strict_Transport_Security_.28HSTS.29
     */
    public $hstsMaxAge = 31536000;
    /**
     * @var bool Whether to apply Strict-Transport-Security rule to all of the site's subdomains as well.
     */
    public $hstsIncludeSubdomains = true;
    /**
     * @var int Whether to prevent Internet Explorer and Chrome from MIME-sniffing a response away from the declared content-type.
     * 1 = on
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-Content-Type-Options
     */
    public $contentTypeLevel = 1;
    /**
     * @var array Content Security Policy directives to be applied.
     * Array items structure is following:
     * 'directive' => "value"
     * Remember that special keywords require single quotes i.e. 'none', 'self', 'unsafe-inline', 'unsafe-eval'
     * Set to empty array, false or null to switch off.
     * https://wiki.mozilla.org/Security/Guidelines/Web_Security#Content_Security_Policy
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#Content-Security-Policy
     */
    public $cspDirectives = [
        'default-src' => "'none'",
        'connect-src' => "'self'",
        'img-src' => "'self'",
        'script-src' => "'self'",
        'style-src' => "'self'"
    ];
    /**
     * @var array HPKP pins.
     * Every item should be Base64 encoded Subject Public Key Information (SPKI) fingerprint.
     * Set to empty array, false or null to switch off.
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#Public_Key_Pinning_Extension_for_HTTP_.28HPKP.29
     */
    public $hpkpPins = [];
    /**
     * @var int Number of seconds browser should remember that this site is only to be accessed using one of the pinned keys.
     * Works only with set $hpkpPins.
     */
    public $hpkpMaxAge = 10000;
    /**
     * @var bool Whether to apply HPKP rule to all of the site's subdomains as well.
     */
    public $hpkpIncludeSubdomains = true;
    /**
     * @var string URL where validation failures are reported to.
     * Set empty to ommit.
     */
    public $hpkpReportUri = '';


    /**
     * @inheritdoc
     */
    public function send()
    {
        if ($this->isSent) {
            return;
        }
        $this->addSafetyHeaders();
        parent::send();
    }
    
    /**
     * Adds safety headers.
     */
    public function addSafetyHeaders()
    {
        $headers = $this->getHeaders();
        $this->addContentSecurityPolicy($headers);
        $this->addStrictTransportSecurity($headers);
        $this->addContentTypeOptions($headers);
        $this->addXssProtection($headers);
        $this->addFrameOptions($headers);
        $this->addPublicKeyPins($headers);
    }
    
    /**
     * Sets CSP header.
     * @param HeaderCollection $headers
     */
    public function addContentSecurityPolicy(HeaderCollection $headers)
    {
        if ($this->cspDirectives && is_array($this->cspDirectives)) {
            $values = [];
            foreach ($this->cspDirectives as $directive => $content) {
                $values[] = $directive . " " . $content;
            }
            $headers->set('Content-Security-Policy', implode("; ", $values));
        }
    }
    
    /**
     * Sets X-Frame-Options header.
     * @param HeaderCollection $headers
     */
    public function addFrameOptions(HeaderCollection $headers)
    {
        if ($this->frameLevel == 3 && defined('YII_DEBUG') && YII_DEBUG == true) {
            // Lower frameLevel for debug module frames
            $this->frameLevel = 2;
        }
        switch ($this->frameLevel) {
            case 3:
                $headers->set('X-Frame-Options', "DENY");
                break;
            case 2:
                $headers->set('X-Frame-Options', "SAMEORIGIN");
                break;
            case 1:
                $headers->set('X-Frame-Options', "ALLOW-FROM {$this->frameAllowFrom}");
                break;
        }
    }
    
    /**
     * Sets X-XSS-Protection header.
     * @param HeaderCollection $headers
     */
    public function addXssProtection(HeaderCollection $headers)
    {
        switch ($this->xssLevel) {
            case 3:
                $headers->set('X-XSS-Protection', "1; report={$this->xssReport}");
                break;
            case 2:
                $headers->set('X-XSS-Protection', "1; mode=block");
                break;
            case 1:
                $headers->set('X-XSS-Protection', "1");
                break;
        }
    }
    
    /**
     * Sets Strict-Transport-Security header.
     * @param HeaderCollection $headers
     */
    public function addStrictTransportSecurity(HeaderCollection $headers)
    {
        if ($this->hstsMaxAge) {
            $value = "max-age={$this->hstsMaxAge}";
            if ($this->hstsIncludeSubdomains) {
                $value .= "; includeSubDomains";
            }
            $headers->set('Strict-Transport-Security', $value);
        }
    }
    
    /**
     * Sets X-Content-Type-Options header.
     * @param HeaderCollection $headers
     */
    public function addContentTypeOptions(HeaderCollection $headers)
    {
        if ($this->contentTypeLevel) {
            $headers->set('X-Content-Type-Options', "nosniff");
        }
    }
    
    /**
     * Sets HPKP header.
     * @param HeaderCollection $headers
     */
    public function addPublicKeyPins(HeaderCollection $headers)
    {
        if ($this->hpkpPins && is_array($this->hpkpPins)) {
            $values = [];
            foreach ($this->hpkpPins as $pin) {
                $values[] = "pin-sha256=\"" . $pin . "\"";
            }
            if (!empty($this->hpkpReportUri)) {
                $values[] = "report-uri=\"" . $this->hpkpReportUri . "\"";
            }
            $values[] = "max-age=" . $this->hpkpMaxAge;
            if ($this->hpkpIncludeSubdomains) {
                $values[] = "includeSubDomains";
            }
            $headers->set('Public-Key-Pins', implode("; ", $values));
        }
    }
}

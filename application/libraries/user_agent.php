<?php
namespace application\Libraries;

/**
 * User agents strings parser. 
 * 
 * The class had been created after i have had analyzed about 100.000 different values of USER_AGENT Environment. 
 * So I have identified the different types of browser's layouts and depending on these types my object parses 
 * the string of HTTP_USER_AGENT Environment to extract propper value of browsers and platforms. 
 * Ofcourse it won't cover all 100% of HTTP_USER_AGENTs but it's enough to cover all popular browsers 
 * including MSIE, Chrome, Firefox, Safari and Opera.
 * 
 * PHP versions 5
 * 
 * Copyright (c) 2009 - 2012, Diptan (Ethere) Vladimir.
 * @package classes
 * @subpackage parsers
 * @author Diptan Vladimir <ethereflawless@gmail.com>
 * @copyright 2009 - 2012, Diptan (Ethere) Vladimir.
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @filesource
 */

/**
 * UserAgentParser Class
 * 
 * @todo Delete useless internal variables.
 * @package classes
 * @subpackage parsers
 * @example example.php
 * @version 1.2.2
 * @access public
 *  
 */
class user_agent {
    /**
     * Binary 1
     * 
     * @constant PRESTO 1  
     */

    const PRESTO = 1;
    /**
     * Binary 10
     * 
     * @constant PRESTO 2  
     */
    const GECKO = 2;
    /**
     * Binary 100
     * 
     * @constant TRIDENT 4  
     */
    const TRIDENT = 4;
    /**
     * Binary 1000
     * 
     * @constant WEBKIT 8  
     */
    const WEBKIT = 8;
    /**
     * Binary 10000
     * 
     * @constant PROPRIETARY 16  
     */
    const PROPRIETARY = 16;
    /**
     * Binary 11111
     * 
     * @constant ALL 31  
     */
    const ALL = 31;
    /**
     * Binary 1111100000
     * 
     * @constant UNDEFINED 992  
     */
    const UNDEFINED = 992;

    /**
     * Browser's types
     * 
     * @access protected
     * @var array 
     */
    protected $types = array(
        self::ALL => 'ALL',
        self::GECKO => 'GECKO',
        self::PRESTO => 'PRESTO',
        self::PROPRIETARY => 'PROPRIETARY',
        self::TRIDENT => 'TRIDENT',
        self::UNDEFINED => 'UNDEFINED',
        self::WEBKIT => 'WEBKIT'
    );

    /**
     * Uses to format an incoming strings into preferred format.
     * 
     * @static
     * @access protected
     * @param string $str A string to be formatted.
     * @return string string
     */
    protected static function format($str) {
        return strtr($str, array(
                    ' ' => '_',
                    '/' => '_',
                    '-' => '_',
                    '!' => ''
                ));
    }

    /**
     * Stored user-agent's string value.
     * 
     * @access protected
     * @var string|null
     */
    protected $userAgent = null;

    /**
     * Browser's value after parsing.
     * 
     * @access protected
     * @var string
     */
    protected $browser = 'undefined';

    /**
     * Number of the browser version represented as a string. 
     * 
     * @access protected
     * @var string 
     */
    protected $version = 'undefined';

    /**
     * Platform's value after parsing.
     * 
     * @access protected
     * @var string
     */
    protected $platform = 'undefined';

    /**
     * Type of browser's layout.
     * 
     * @access protected
     * @var string
     */
    protected $type = 992;

    /**
     * The full names of some platforms's abbreviations.
     * 
     * @access protected
     * @var array
     */
    protected $inOS = array('s60' => 'symbian', 'series60' => 'symbian', 'x11' => 'linux', 'winnt' => 'windows_nt', 'mac' => 'macintosh', 'i686' => 'linux_i686');

    /**
     * The full names of some browsers's abbreviations.
     * 
     * @access protected
     * @var array
     */
    protected $inBS = array('yie8' => 'msie_8', 'theworld' => 'theworld_browser', 'phaseout_www.phaseout.net' => 'phaseout', 'acc=vonner' => 'vonner_spider', 'teleca' => 'teleca_obigo_browser');

    /**
     * Setter for UserAgentParser::$userAgent. 
     * An incoming data will be casted to the String type and the string will be trimmed and converted to lower case.
     * Fluent Interface.
     * 
     * @access public
     * @see UserAgentParser::$userAgent
     * @param mixed $userAgent A string having an user agent value.
     * @return UserAgentParser Returns <b>UserAgentParser</b>.
     */
    public function setUserAgent($userAgent = null) {
        if ($userAgent !== null)
            $this->userAgent = trim(strtolower((string) $userAgent));
        return $this;
    }

    /**
     * Setter for UserAgentParser::$browser.
     * The browser's raw value will be formatted.
     * Fluent Interface.
     * 
     * @access protected
     * @see UserAgentParser::$browser
     * @param string $browser The value of the browser.
     * @return UserAgentParser Returns <b>UserAgentParser</b>.
     */
    protected function setBrowser($browser) {
        $browser = preg_replace('%^(?:http:__www\.)?((?:[^\d]+)(?:\d+(\.\d+)*)?).*%', '$1', self::format($browser));
        $this->browser = !isset($this->inBS[$browser]) ? $browser : $this->inBS[$browser];
        $match = array();
        if (preg_match('%(?:\d\.?)+%', $this->browser, $match)) {
            $this->version = $match[0];
        }
        return $this;
    }

    /**
     * Setter for UserAgentParser::$platform.
     * The platform's raw value will be formatted.
     * Fluent Interface.
     * 
     * @todo add other linux distributions
     * @access protected
     * @see UserAgentParser::$platform
     * @param string $platform The value of the platform
     * @return UserAgentParser Returns <b>UserAgentParser</b>.
     */
    protected function setPlatform($platform) {
        if (strpos($this->userAgent, 'linux') !== false and strpos($this->userAgent, 'gecko') !== false) {
            $match = array();
            if (preg_match('%(?:ubuntu|xubuntu|kubuntu|fedora|mandriva|pclinuxos|debian|suse|mint)(?:(?:/|-|\s)[\d\.]+)?%', $this->userAgent, $match)) {
                $platform .= '_' . $match[0];
            }
        } elseif (isset($this->inOS[$platform])) {
            $platform = $this->inOS[$platform];
        }
        $this->platform = self::format($platform);
        return $this;
    }

    /**
     * Setter for UserAgentParser::$type.
     * Fluent Interface.
     * 
     * @access protected
     * @see UserAgentParser::$type
     * @param string $type The type of browser's layout.
     * @return UserAgentParser Returns <b>UserAgentParser</b>.
     */
    protected function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Constructor.
     * 
     * @access public
     * @param mixed $userAgent We can pass the data in to constructor to be parsed.
     * @return void
     */
    function __construct($userAgent = null) {
        $this->setUserAgent($userAgent);
    }

    /**
     * Parse user-agent's string.
     * The return depends on arguments were being passed to the method. By default supports Fluent interface.
     * 
     * @todo Create overloaded method.
     * @access public
     * @param mixed $userAgent We can pass a data into the method just before parsing process.
     * @param bool $returnData By default it's false. If it's true then method returns array with prepared output data.
     * @return UserAgentParser|array Returns <b>UserAgentParser</b>, but could return an <b>array</b> with result.
     */
    public function parse($userAgent = null, $returnData = false) {
        $this->setUserAgent($userAgent);
        if ($this->userAgent !== null) {
            $this->browser = $this->version = $this->platform = 'undefined';
            $this->type = self::UNDEFINED;
            ($this->isBasicPFLayout()
                    or $this->isCompatiblePFLayout());
            ($this->isPrestoLayout()
                    or $this->isWebkitLayout()
                    or $this->isGeckoLayout()
                    or $this->isTridentLayout()
                    or $this->isProprietaryLayout());
            if ((bool) $returnData)
                return $this->getParsedData();
        }
        return $this;
    }

    /**
     * Parse "Proprietary" layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isProprietaryLayout() {
        $match = array();
        $result = false;
        if (preg_match('%.*?(?:compatible)?\s*([\w\s]*(?:twiceler/ro)?bot\w*(?:/[\w\.]+)?)|.*?([\w\s]*spider\w*(?:/[\d\.]+)?)|.*(dolfin/[\d\.]+)|.*(abrowse\s*[\d\.]*)|.*?([\w\.]*browser(?:/[\w\.]+)?)|.*?((?:obigo|teleca)/[\w\.]+)|^([\w\s\.\-\!]+(?:/[\w\.]+)?)%', $this->userAgent, $match)) {
            foreach (array_slice($match, 1) as $found)
                if (isset($found) and !empty($found)) {
                    $result = $found;
                    break;
                }
        }
        if ($result) {
            $this
                    ->setBrowser($result)
                    ->setType(self::PROPRIETARY);
            return true;
        }
        return false;
    }

    /**
     * Parse "Webkit" layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isWebkitLayout() {
        if (!stristr($this->userAgent, 'webkit'))
            return false;
        $regex = '%(?:(internet|element|google)\s)?(?(?=[a-z]{4,}\b(?!(?:/|\s)\d))(\w+(?(?<=omniweb)(?:/v[\d\.]+)?)|(\w+))|\w{4,}(?<!applewebkit|debian|mobile|suse|untrusted|ubuntu|fedora)(?:/|\s)[\d\.]+)%i';
        $clean = '%\([^()]+\)|qtweb|compatible|android|fb[a-z]+|like|mobile|!|http://(?:[^\.]+\.)+[a-z]+|safari/[^\s]+%i';
        $browser = trim(array_pop(explode('webkit', $this->userAgent)));
        $temp = preg_replace($clean, '', $browser);

        $result = false;
        $match = array();
        if (preg_match_all($regex, $temp, $match)) {
            $find = $match[0];
            if ($find[0] and !strstr($find[0], 'version')) {
                $result = $find[0];
            } elseif (strncmp($find[0], 'version', 7) === 0 and !$find[1]) {
                $result = str_replace('version', 'safari', $find[0]);
            } elseif ($find[1]) {
                $result = $find[1];
            }
        } elseif (preg_match('%safari(?!.*\))%', $browser, $match)) {
            $result = $match[0] . '_2';
        }
        if ($result) {
            if (strstr($browser, 'mobile')) {
                $result = 'mobile_' . $result;
            }
            $this
                    ->setBrowser($result)
                    ->setType(self::WEBKIT);
            return true;
        }
        return false;
    }

    /**
     * Parse "Gecko" layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isGeckoLayout() {
        if (!preg_match('%gecko/\d+.+%', $this->userAgent))
            return false;
        $regex = '%(?:maemo\s|cs\s200|k-)?\w+(?<!firephp|pbstb|webde|autopager|mozilla|mra|abelssoft|anonymisierer|maulkorb|webkit|mnenhy|jingoo|lightning|linux|debian|suse|pclinuxos|runtime|ubuntu|mandriva|mint|fedora)(?:/|\s)[\d\.]+%i';
        $clean = '%\((?!palemoo|swiftfo)[^()]+\)|askt[\w\-]+|3p_\w+|\s\d{7,}\s?|-vision|(?:[a-z]+)?toolbar%i';
        $browser = trim(array_pop(explode('gecko', $this->userAgent)));
        $temp = preg_replace($clean, '', $browser);
        $match = array();
        $result = false;
        if (preg_match_all($regex, $temp, $match)) {
            $find = $match[0];
            if (isset($find[1]) and !strstr($find[1], 'firefox')) {
                $result = $find[1];
                if (stristr($result, 'sputnik')) {
                    $result = 'firefox_' . $result;
                }
            } elseif ($find[0]) {
                $result = $find[0];
            }
        }
        if ($result) {
            $this
                    ->setBrowser($result)
                    ->setType(self::GECKO);
            return true;
        }
        return false;
    }

    /**
     * Parse "Presto" layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isPrestoLayout() {
        if (strpos($this->userAgent, 'opera') === false)
            return false;
        $regex = '%^(opera)[/\s]([\d\.]+)(?(?!.*opera).*\)(?:\spresto.*version/([\d\.]+))?|.*(opera\s(?:mobi|mini|tablet)(?:/[\w\.]+)?))%i';
        $match = array();
        $result = false;
        if (preg_match($regex, $this->userAgent, $match)) {
            if (isset($match[4])) {
                $result = $match[4];
            } elseif ($match[2] or $match[3]) {
                $result = ( $match[1] . '_' . ( $match[3] ? $match[3] : $match[2] ) );
            }
        }
        if ($result) {
            $this
                    ->setBrowser($result)
                    ->setType(self::PRESTO);
            return true;
        }
        return false;
    }

    /**
     * Parse "Trident" layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @todo reorganize $ndenied
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isTridentLayout() {
        $clean = '%\s*(?:\([^()]+\)(?=.*;)|([a-z]{2})\1\b|msn[\w\-]+|hp\wtdf|q\d+|ub\w|askt\w*|ma\wm|q\d+|\w[\w\s\_\.]*(tool)?bar|\w*dialer|[a-z]{2}\b-[a-z]{2}\b|enuson(?:net|com)|\+?http\:\/\/(?!.*?abolimba)|;\s*[a-z]{4}(?=\)))[^;)]*%i';
        $regex = '%^.*?mozilla/.*?\(\s*(?:compatible;|mozilla[^;]*;|windows;)?(?:\s*u;)?(?:\s*(msie[^;]+))?(.*?\))\s*(?(?!/?\d+|applewebkit|khtml|version|gecko|whistler)([\w\s\./]{4,}(?!.+;))?)%i';
        $temp = preg_replace($clean, '', $this->userAgent);
        $match = array();
        $result = false;
        if (preg_match($regex, $temp, $match)) {
            $nmatch = array();
            if (isset($match[3]) and !empty($match[3])) {
                $result = $match[3];
            } elseif (preg_match_all('%([a-z]+)[^;)]*%i', $match[2], $nmatch)) {
                $denied = array('net', 'u', 'i', 'n', '3p_uvrm', 'akbank', 'alexa', 'amd', 'amsavs', 'antivirxp', 'apc', 'arcor',
                    'atyinst', 'baidugame', 'bo', 'boie', 'box', 'bri', 'bristol', 'btrs', 'build', 'chromeframe', 'ciba',
                    'cmdtdf', 'cmntdf', 'cognosrcp', 'coinbox', 'compat', 'cpdtdf', 'cpntdf', 'creative', 'crossrider',
                    'customie', 'dealio', 'desktopsmiley', 'digext', 'ds_', 'easybits', 'embeddedwb', 'enca', 'engb',
                    'engine', 'enus', 'enusbingip', 'enusmscom', 'enussem', 'esobisubscriber', 'esxl', 'etoolkit', 'fbsmtwb',
                    'gmx', 'pos', 'fctb', 'fdm', 'feed', 'frbe', 'free', 'funwebproducts', 'funwebproductss', 'gamexn',
                    'geopt', 'geoptimaliseerd', 'gtb', 'handycafecfw', 'ety', 'handycafecln', 'haoetv', 'hbtools', 'hpntdf',
                    'hyves', 'icafemedia', 'iemb', 'image', 'iopus', 'infopath', 'internet', 'iph', 'ipms', 'itit',
                    'ititmse', 'jajp', 'nsn', 'kit', 'layout', 'library', 'linux', 'myie', 'maam', 'maar', 'maau',
                    'macintosh', 'magw', 'malc', 'maln', 'mamd', 'mami', 'mapb', 'masa', 'masp', 'matp', 'mcit1', 'mddc',
                    'mddr', 'mdds', 'media', 'mediacenter', 'microsoft', 'mj', 'mozilla', 'mra', 'mrie', 'mrsputnik', 'msie',
                    'msiec', 'msn', 'nap', 'naviwoo', 'nbno', 'nbnomse', 'neostrada', 'net', 'qxw', 'netclr', 'np', 'ntl',
                    'ntlworld', 'officeliveconnector', 'officelivepatch', 'optusie', 'orange', 'patch', 'pbstb', 'peoplepal',
                    'ptbr', 'ptbrmse', 'puw', 'qqdownload', 'qqpi', 'qqpinyin', 'qwestie', 'ringo', 'runtime', 'rv',
                    'seeearchx', 'seekmo', 'shopperreports', 'sicent', 'simbar', 'sky', 'slcc', 'son', 'spamblockerutility',
                    'sprint', 'sr', 'srs', 'stb', 'sv', 'svse', 'system', 'tablet', 'thmdr', 'tnet', 'tob', 'trident',
                    'trtrmse', 'ub', 'ue', 'update', 'uqfulhrido', 'uqhsneokuq', 'uqjepkuqgr', 'usdr', 'user_agent', 'ver',
                    'version', 'visor', 'visualtb', 'webde', 'webmoney', 'win', 'windows', 'winfc', 'winnt', 'wintsi', 'wfx',
                    'winue', 'woshihoney', 'wow', 'wwtclient', 'x', 'x64', 'xblwp', 'xf', 'ycomp', 'yjsg', 'ypc', 'ytb',
                    'zango', 'zemanaaid', 'zhcn', 'zune');
                $search = array_unique($nmatch[1]);
                $diff = each(array_diff($search, $denied));
                if (isset($diff) and !empty($diff)) {
                    $result = $nmatch[0][$diff['key']];
                } else {
                    if (!$result and ( isset($match[1]) and !empty($match[1]) )) {
                        $result = $match[1];
                    } elseif (stristr('iphone|ipad|ipod', $result) and preg_match('%^.*applewebkit.*mobile/(\d)%i', $this->userAgent, $match)) {
                        $result .= '_safari_mobile_' . $match[1];
                    }
                }
            }
        }
        if ($result) {
            $this
                    ->setBrowser($result)
                    ->setType(self::TRIDENT);
            return true;
        }
        return false;
    }

    /**
     * Parse "basic" platform's layouts.
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isBasicPFLayout() {
        $regex = '%[^(]+\(\s*(?:windows;)?(?:\s*msie.*?;)?\s*(s60|ip(?:a|o)d|sch[^;]+|android(?:[^;)]+)?|iphone|j2me/midp|win\b|sistema\sfenix|charon|windows(?:[^;)]+)?|symbianos(?:[^;)]+)?|macintosh|sanyo|x11|(?:arch\s)?linux(?:[^;)]+)?)(?:;(?:\s*u;)?)?\s*(?:\s*msie[^;]+;)?(?(?!wow64|ppc|opera|g11|series60|win64|x64|mra)([\w\s\.]+)?)%i';
        $match = array();
        $result = false;
        if (preg_match($regex, $this->userAgent, $match)) {
            if (isset($match[2]) and isset($match[2][2])) {
                $result = $match[2];
            } elseif (isset($match[1]) and !empty($match[1])) {
                $result = $match[1];
            }
        }
        if ($result) {
            $this->setPlatform($result);
            return true;
        }
        return false;
    }

    /**
     * Parse "compatible" platform's layouts
     * Returns true if result is been caught, otherwise returns false.
     * 
     * @access protected
     * @return bool Returns boolean.
     */
    protected function isCompatiblePFLayout() {
        $regex = '%.*?\s\((?:compatible|windows)?.*?(?:msie|abrowse|origyn)[^;]+(?:;\s*(?:aol)[^;]+)*;\s*(?(?=x11;)(?:\s*([^;)]+)[;)]){2}|([^;)]+))%i';
        $match = array();
        $result = false;
        if (preg_match($regex, $this->userAgent, $match)) {
            if (isset($match[1]) and !empty($match[1])) {
                $result = $match[1];
            } elseif (isset($match[2]) and !empty($match[2])) {
                $result = $match[2];
            }
        }
        if ($result) {
            $this->setPlatform($result);
            return true;
        }
        return false;
    }

    /**
     * Returns serialized data if UserAgentParser's object is called as a string.
     * 
     * @see UserAgentParser::getParsedData()
     * @access public
     * @return string Returns serialized data.
     */
    public function toString() {
        return serialize($this->getParsedData());
    }

    /**
     * @see UserAgentParser::toString()
     * @access public
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Getter for UserAgentParser::$browser.
     * Will return the signature of the detected browser.
     * 
     * @see UserAgentParser::$browser
     * @access public
     * @return string Returns browser's signature.
     */
    public function getBrowser() {
        return $this->browser;
    }

    /**
     * Getter for UserAgentParser::$version.
     * Will return browser version if exists.
     * 
     * @see UserAgentParser::$version
     * @access public
     * @return string Returns browser's version.
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Getter for UserAgentParser::$platform.
     * Will return the signature of the detected platform.
     * 
     * @see UserAgentParser::$platform
     * @access public
     * @return string Returns platform's name.
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * Getter for UserAgentParser::$type.
     * The return depends on argument was being passed to the method. By default returns string.
     * Use the class constants to check what type of browser has been caught.
     * 
     * @see UserAgentParser::$type
     * @access public
     * @return string|bool By default returns <b>string</b> with name of type. If you will use class's constants for comparing matched types then will return <b>boolean</b> value.
     */
    public function getType($const = null) {
        if ($const !== null) {
            return (bool) ($this->type & $const);
        }
        return $this->types[$this->type];
    }

    /**
     * Getter for common output data.
     * Will return array with associative and numeric indexes.
     * Also used when object called as a string.
     * 
     * @see UserAgentParser::toString()
     * @access public
     * @return array Returns <b>array</b> with both numeric and associative keys.
     */
    public function getParsedData() {
        return array(
            $this->getBrowser(),
            'browser' => $this->getBrowser(),
            $this->getVersion(),
            'version' => $this->getVersion(),
            $this->getPlatform(),
            'platform' => $this->getPlatform(),
            $this->getType(),
            'type' => $this->getType()
        );
    }

    /**
     * Use this method to test browser's name and version. 
     * You can set the third argument to <b>true</b> and test a version more precisely till the last sign.
     * 
     * @param string $signature
     * @param number $version
     * @param bool $strict
     * @return bool Returns <b>true</b> if matched otherwise returns <b>false</b>.
     */
    public function is($signature, $version = null, $strict = false) {
        $matchVersion = true;
        if ($version !== null) {
            $temp = explode('\.', preg_quote((string) $version));
            $regex = '%^' . implode('(?=\.)\.', $temp);
            if ($strict) {
                $regex .= '(?!\d+)';
            }
            $regex .= '%i';
            $matchVersion = preg_match($regex, $this->getVersion()) === 1;
        }
        return !(stripos($this->getBrowser(), $signature) === false) && $matchVersion;
    }

}
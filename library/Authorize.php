<?php
/**
 * 微信网页授权类
 *
 * @author: lion
 * @link: http://lionsay.com/codetoany.html
 */

namespace lion\weixin\library;

class Authorize
{
	private $_isFromWeixinGetParamName = '__is_from_weixin_lion__';
	private $_isFromWeixinGetParamValue = 'yes';

	public $appId;
	public $isHttps = false;

	public function __construct($appId)
	{
		$this->appId = $appId;
	}

	public function authorizeCodeToUrl(array $authorizeUrlConfig = [], $authorizeUrlGetParamName = 'auk', $isOverrideAuthorizeUrlGetParam = false, $h5Config = [])
	{
		$finalAuthorizeUrl = '';
		$authorizeUrlKey = isset($_GET[$authorizeUrlGetParamName]) ? $_GET[$authorizeUrlGetParamName] : NULL;
		if (!empty($_GET[$this->_isFromWeixinGetParamName]) && $_GET[$this->_isFromWeixinGetParamName] == $this->_isFromWeixinGetParamValue) {
			if (!empty($_GET[$authorizeUrlGetParamName])) {
				if (!empty($authorizeUrlConfig[$authorizeUrlKey])) {
					$finalAuthorizeUrl = $authorizeUrlConfig[$authorizeUrlKey];
					$finalAuthorizeUrl = $this->_getAuthorizeUrl($finalAuthorizeUrl, $authorizeUrlGetParamName, $isOverrideAuthorizeUrlGetParam);
				}
			}
		} else {
			if ( isset($h5Config[$authorizeUrlKey]) ) {
				// 判断是否是H5站点
				$finalAuthorizeUrl = $this->_getAuthorizeUrl($h5Config[$authorizeUrlKey], $authorizeUrlGetParamName, $isOverrideAuthorizeUrlGetParam);
			} else {
				// 微信授权
				$apiGetParamState = empty($_GET['state']) ? 'STATE' : $_GET['state'];
				unset($_GET['state']);
				$_GET[$this->_isFromWeixinGetParamName] = $this->_isFromWeixinGetParamValue;
				$apiGetParamRedirectUrl = explode('?', $_SERVER['REQUEST_URI']);
				$apiGetParamRedirectUrl = 'http' . ($this->isHttps ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $apiGetParamRedirectUrl[0] . '?' . urldecode(http_build_query($_GET));
				$apiGetParam['appid'] = $this->appId;
				$apiGetParam['redirect_uri'] = urlencode($apiGetParamRedirectUrl);
				$apiGetParam['response_type'] = 'code';
				$apiGetParam['scope'] = empty($_GET['scope']) || !in_array($_GET['scope'], ['snsapi_base', 'snsapi_userinfo']) ? 'snsapi_base' : $_GET['scope'];
				$apiGetParam['state'] = "{$apiGetParamState}#wechat_redirect";
				$finalAuthorizeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . urldecode(http_build_query($apiGetParam));
			}
		}
		if ($finalAuthorizeUrl) {
			header("Location: {$finalAuthorizeUrl}");
		}
	}

	protected function _getAuthorizeUrl($finalAuthorizeUrl, $authorizeUrlGetParamName='auk', $isOverrideAuthorizeUrlGetParam=FALSE) {
		$filterGetParamName = [$this->_isFromWeixinGetParamName, $authorizeUrlGetParamName];
		$forceGetParamName = ['code', 'state'];
		$newGetParam = [];
		foreach ($_GET as $k => $v) {
			if (in_array($k, $forceGetParamName) || (!in_array($k, $filterGetParamName) && ($isOverrideAuthorizeUrlGetParam || !preg_match("/[\?|\&]{$k}\=/", $finalAuthorizeUrl)))) {
				$newGetParam[$k] = $v;
			}
		}
		if ($newGetParam) {
			if ( isset($newGetParam['page']) ) {
				$page = $newGetParam['page'];
				unset($newGetParam['page']);
				$fixParams = array(
					'code',
					'state',
					'uuid',
					'puid',
				);
				$fixParamsArr = array();
				foreach( $fixParams as $v ) {
					if ( isset($newGetParam[$v]) ) {
						$fixParamsArr[$v] = $newGetParam[$v];
						if ( in_array($v, $forceGetParamName) ) unset($newGetParam[$v]);
					}
				}
				$finalAuthorizeUrl .= '?' . http_build_query($fixParamsArr);
				$finalAuthorizeUrl .= "&#/?path={$page}&" . http_build_query($newGetParam);
			} else {
				$finalAuthorizeUrl .= (strpos($finalAuthorizeUrl, '?') === false ? '?' : '&') . http_build_query($newGetParam);
			}
		}
		return $finalAuthorizeUrl;
	}
}

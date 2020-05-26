# **CodeToAny**：**一个**微信公众号网页授权给**任何**域名下的url
把从微信网页授权接口中获取到的`授权code`以get参数的形式传递给**任何**域名下的url。

[原git地址][1]

# 二次调整

在源码基础上自己做了一些优化与调整，自己项目是作为多个域名的授权中转使用，突破微信自身的授权域名数量限制

多个域名均使用一个微公号授权，都通过此中转

* 单独封装出配置文件`config.php`
* 支持H5站的跳转（按需使用）

## 环境需求
* php >= 5.4.0（小于5.4.0的情况可以联系作者解决）；

## 极速使用

1. 假设打算通过`www.test.com`域名（其域名已设置为网页授权回调域名）作为其他域名（paperen.com）站点的中转；
2. 编辑`config.php`，将变量`appid`的值修改为自己的`微信公众号AppId`；
3. 将`config.php`的`wx`部分修改`test`键值为`http://a.com`；
4. 在微信内或使用微信web开发者工具访问`http://www.test.com/?auk=test`，顺利的话，页面将跳转到类似这样的url：`http://paperen.com/?abc=123&code=0318PVx00bTFzB1JOny00YMRx008PVxS&state=STATE`；

## 攻略指南
1. `?auk=demo1`中的`auk`、`demo1`以及此时的`授权url`（即接收`授权code`的url，最终跳转的url）都是可以自定义的；
2. 网页授权接口中的get参数`scope`和`state`可以以get参数的形式传递给`codetoany/getcode.php`，程序会把它们再传递给接口；
3. 除了get参数`auk`外，传递给`codetoany/getcode.php`的任何get参数都会以get参数的形式再传递给`授权url`；
4. 如果网页授权回调域名使用`https`协议访问，那么程序需要略微调整才可以正常使用；

[1]: <https://github.com/lionskys/codetoany> "代码源"

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'API 使用向导';
$this->params['breadcrumbs'][] = ['label' => 'Member', 'url' => ['info']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-guide-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <h3>1. 鉴权</h3>
    <h4>1.1 介绍</h4>
    <p>接口通过参数中的 <var>apiKey</var> 及 <var>apiSign</var> 验证接口权限，每一个接口除业务需要的参数外，还必须传递这2个参数。根据各接口需求不同，可通过 POST 或 GET 方式进行传递。<var>apiKey</var> 用于标识用户的唯一身份， <var>apiSign</var> 用于对本次访问的数据进行校验。</p>

    <h4>1.2 参数说明</h4>
    <table class="table table-hover">
        <tbody class="col-lg-6">
            <tr>
                <td class="text-right col-lg-1"><var>apiKey</var></td>
                <td>通过后台查看用户信息时获得，系统通过此参数辨别用户</td>
            </tr>
            <tr>
                <td class="text-right"><var>apiSecret</var></td>
                <td>通过后台查看用户信息时获得，用于计算请求签名参数。请勿泄露。</td>
            </tr>
            <tr>
                <td class="text-right"><var>apiSign</var></td>
                <td>请求签名，通过每个接口的参数和 <var>apiSecret</var> 计算得出</td>
            </tr>
        </tbody>
    </table>

    <h4>1.3 签名计算</h4>
    <p>在每次请求中，都需要提交 <var>apiSign</var> 参数。<var>apiSign</var> 参数通过 <var>apiSecret</var> 和接口参数计算生成。 </p>
    <p>计算方法：</p>
    <p>
        <ul>
            <li>把所有需要提交的参数，根据参数名进行排序</li>
            <li>先将参数值进行URL编码，再将参数名及参数值，使用 “<var>=</var>” 连接，形成一对数据</li>
            <li>接着，使用 “<var>&amp;</var>” 连接每一对数据，形成参数字符串</li>
            <li>在参数字符串最后补充上 “<var>&amp;apiSecret=xxx</var>” （xxx为apiSecret的值），形成预校验字符串</li>
            <li>对预校验字符串进行 MD5 运算，获得 32 位小写的值，即为 <var>apiSign</var> </li>
        </ul>
    </p>
    <p>简单来讲，就是先对请求的参数根据参数名排序，以此顺序拼接成 GET 请求的参数，在字符串的最后加上 “<var>&amp;apiSecret=xxx</var>” （xxx为apiSecret的值），即为预校验字符串。对预校验字符串进行 MD5 计算，获得 32 位校验值，就是 <var>apiSign</var> </p>
    <p>举例说明：</p>
    <p>
        假定一个接口需要提交的参数，一共有 2 个参数，分别是 <var>province</var> 和 <var>country</var>
        <pre>province=北京
country=china
</pre>
我们先对上述参数根据参数名排序，对参数值进行 URL 编码，并拼接成字符串，得到
<pre>country=china&amp;province=%E5%8C%97%E4%BA%AC</pre>
在此字符串的最后追加上 <var>&amp;apiSecret=xxx</var> （xxx为apiSecret的值），那么最终将得到
<pre>country=china&amp;province=%E5%8C%97%E4%BA%AC&amp;apiSecret=xxx</pre>
最后计算上面字符串的 MD5 值，取 32 位，并全部转换为小写，就是 <var>apiSign</var>
    </p>
    <p>最终请求的参数，还要加上 <var>apiSign</var> 及 <var>apiKey</var>，以 HTTP GET 的 query param 显示就是 </p>
    <pre>country=china&amp;province=%E5%8C%97%E4%BA%AC&amp;apiSign=a278502c04e9635790fa7783937f1d32&amp;apiKey=apikeystring</pre>
    <p></p>

    <h3>2. 返回数据</h3>
    <h4>2.1 返回格式</h4>
    <p>数据返回格式采用动态响应，支持 XML 和 JSON 2种。系统会根据客户端发送 HEADER 中 <var>Accept</var> 的值进行自动适配。例如使用curl命令，请求 API ：</p>
    <pre>curl -i -H 'Accept: application/json;' 'http://tag.id5.cn/api/v1/some/method?input=value&amp;apiSign=46fab4792ce6f1000abc5e314be85c7d&amp;apiKey=xxxxxx'</pre>
    <p>将返回</p>
    <pre>
HTTP/1.1 200 OK
Date: Fri, 26 Aug 2016 03:54:34 GMT
Content-Type: application/json; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive

{"error":"0","message":"","data":{"mobile":13800138000}}</pre>
    <p>如果改变 HEADER 中 Accept 为 <var>application/xml</var> ：</p>
<pre>curl -i -H 'Accept: application/xml;' 'http://tag.id5.cn/api/v1/some/method?input=value&amp;apiSign=46fab4792ce6f1000abc5e314be85c7d&amp;apiKey=xxxxxx'</pre>
    <p>接口将以 XML 格式返回：</p>
    <pre>
HTTP/1.1 200 OK
Date: Fri, 26 Aug 2016 03:54:34 GMT
Content-Type: application/json; charset=UTF-8
Transfer-Encoding: chunked
Connection: keep-alive

&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;
&lt;response&gt;&lt;error&gt;0&lt;/error&gt;&lt;message&gt;&lt;/message&gt;&lt;data&gt;&lt;mobile&gt;13800138000&lt;/mobile&gt;&lt;/data&gt;&lt;/response&gt;</pre>

    <p>如果不传递 <var>Accept</var> 将默认以 JSON 格式返回。</p>
    <h4>2.2 数据说明</h4>
    <table class="table table-condensed table-hover">
        <tbody class="col-md-4">
            <tr>
                <td class="col-md-1"><var>error</var></td>
                <td>错误代码，为“0”时，表示返回正常结果</td>
            </tr>
            <tr>
                <td><var>message</var></td>
                <td>错误信息描述</td>
            </tr>
            <tr>
                <td><var>data</var></td>
                <td>业务数据</td>
            </tr>
        </tbody>
    </table>

    <h4>2.3 错误代码列表</h4>
    <table class="table table-condensed table-hover">
        <tbody class="col-md-12 col-lg-10">
            <tr>
                <td class="col-md-2 text-right">0</td>
                <td>没有错误</td>
            </tr>
            <tr>
                <td class="text-right">401</td>
                <td>认证失败</td>
            </tr>
            <tr>
                <td class="text-right">403</td>
                <td>认证用户不允许访问该API终端</td>
            </tr>
            <tr>
                <td class="text-right">404</td>
                <td>请求资源不存在</td>
            </tr>
            <tr>
                <td class="text-right">405</td>
                <td>方法未许可，请检查 Allow 头中许可的 HTTP 方法</td>
            </tr>
            <tr>
                <td class="text-right">415</td>
                <td>不支持的媒体类型，请求内容类型或版本号无效</td>
            </tr>
            <tr>
                <td class="text-right">422</td>
                <td>数据验证失败（比如对于一个 POST 请求），请检查应答body中的错误详细描述</td>
            </tr>
            <tr>
                <td class="text-right">429</td>
                <td>请求过多，请求因超出速率限制而被拒绝</td>
            </tr>
            <tr>
                <td class="text-right">500</td>
                <td>内部错误，这通常是服务器程序内部错误</td>
            </tr>
            <tr>
                <td class="text-right">40101</td>
                <td>用户不存在，无法通过 <var>apiKey</var> 获取用户信息</td>
            </tr>
            <tr>
                <td class="text-right">40102</td>
                <td>缺少签名参数 <var>apiSign</var></td>
            </tr>
            <tr>
                <td class="text-right">40103</td>
                <td>缺少签名参数 <var>apiSign</var> 校验错误</td>
            </tr>
            <tr>
                <td class="text-right">40104</td>
                <td>客户端 IP 地址鉴权失败，不允许访问此接口</td>
            </tr>
            <tr>
                <td class="text-right">40105</td>
                <td>缺少 <var>apiKey</var> 参数</td>
            </tr>
        </tbody>
    </table>
    <h3>3. API 列表</h2>
    <p><var>apiKey</var> 和 <var>apiSign</var> 为每个接口必须传递的参数，下述接口的请求参数中不再重复要求。</p>
    <h4>3.1 解码手机号MD5</h4>
    <p>对手机号的MD5值进行解码，返回MD5编码之前的手机号，支持11位和以86开头的13位手机号，结果以11位的手机号返回</p>
    <h5>URL</h5>
    <pre>http://tag.idtag.cn/api/mobile/md5decode</pre>
    <h5>HTTP请求方式</h5>
    <pre>GET</pre>
    <h5>请求参数</h5>
    <table class="table table-bordered table-condensed table-hover">
        <tbody>
            <tr>
                <td width="100">参数名</td>
                <td width="100">必填</td>
                <td width="100">类型</td>
                <td>说明</td>
            </tr>
            <tr>
                <td>md5</td>
                <td>true</td>
                <td>string</td>
                <td>手机号的MD5，32位全小写</td>
            </tr>
        </tbody>
    </table>
    <h5>返回结果举例（JSON）</h5>
    <pre>{"error":"0","message":"","data":{"mobile":13428215939}}</pre>
    <h5>返回字段说明</h5>
    <table class="table table-bordered table-condensed table-hover">
        <tbody>
            <tr>
                <td width="100">参数名</td>
                <td width="100">类型</td>
                <td>说明</td>
            </tr>
            <tr>
                <td>mobile</td>
                <td>string</td>
                <td>手机号</td>
            </tr>
        </tbody>
    </table>

    <h4>3.2 手机号机主的标签</h4>
    <p>输入手机号或手机号的MD5，返回机主标签</p>
    <h5>URL</h5>
    <pre>http://tag.idtag.cn/api/mobile/sketch</pre>
    <h5>HTTP请求方式</h5>
    <pre>GET</pre>
    <h5>请求参数</h5>
    <table class="table table-bordered table-condensed table-hover">
        <tbody>
            <tr>
                <td width="100">参数名</td>
                <td width="100">必填</td>
                <td width="100">类型</td>
                <td>说明</td>
            </tr>
            <tr>
                <td>mobile</td>
                <td>true</td>
                <td>string</td>
                <td>手机号的MD5或11位手机号</td>
            </tr>
        </tbody>
    </table>
    <h5>返回结果举例（JSON）</h5>
    <pre>{"error":"0","message":"","data":[{"item":"年龄段","remark":"25-29"},{"item":"星座","remark":"天秤座"},{"item":"性别","remark":"男"}]}</pre>
    <h5>返回字段说明</h5>
    <table class="table table-bordered table-condensed table-hover">
        <tbody>
            <tr>
                <td width="100">参数名</td>
                <td width="100">类型</td>
                <td>说明</td>
            </tr>
            <tr>
                <td>item</td>
                <td>string</td>
                <td>数据项</td>
            </tr>
            <tr>
                <td>remark</td>
                <td>string</td>
                <td><p>标识</p>
                    <p>当 <var>item</var> 为“学历”时， <var>remark</var> 值为数字，代表的意义如下：</p>
                    <ol>
                        <li>普通</li>
                        <li>成人</li>
                        <li>研究生</li>
                        <li>网络教育</li>
                        <li>自学考试</li>
                        <li>开放教育</li>
                        <li>不详</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

</div>
<style type="text/css">
var {
    font-style: italic;
    background-color: #eee;
    padding: 0 5px;
}
</style>
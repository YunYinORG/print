## 代码规范

### 命名
* 类文件：首字母大写驼峰，并以‘.class.php’为后缀
* 类名：首字母大写驼峰(```$CodingStandars```)
* 普通变量名：全小写下划线
* 类变量：首字母小写驼峰(```$codingStandars```)
* 实例化对象：首字母大写(```$User=D('User')```)
* 类方法：首字母小写驼峰
* 类私有方法:_开头首字母小写驼峰
* 公共函数：全小写下划线(```coding_standars()```)
* 参数名：与变量名保持一致
* 全局配置和常量：全大写下划线(```CODING_STANDARS```)

### 布局
* 符号之间加空格($i = $j而不是$i=$j)
* 提前抛出错误：$this->error等
* 大括号独占一行：

```php
function f($param)
{
    if($condition)
    {
       // ...
    } 
    else
    {
        //...
    }
    while($condition)
    {
       // ...
    }
}
```

### 注释

* 段落注释: `/*注释内容*/` 位于解释代码的上方独占一行
* 行末注释: `//注释内容` 解释前面这一句话
* 函数文档注释：

```php
/**
 * 函数用途
 * @method functionname
 * @param  {类型}     $p [说明]
 * @return {[type]}        [description]
 * @author 作者[联系邮箱]
 */
function functionname($p)
{
    return $p;
}

```

### 页首说明
页最开始包含文件说明

页头应该包含函数列表

### 编辑器和配置

推荐编辑器`sublime text 3`

插件 `phpfmt` ,`DocBlockr`

phpfmt(格式化php,快捷键 crtl+f11)配置:

```json

{
    "enable_auto_align": true,
    "format_on_save": false,
    "indent_with_space": true,
    "laravel_style": true,
    "passes":
    [
        "SpaceBetweenMethods",
        "MergeElseIf",
        "DoubleToSingleQuote"
    ],
   // "php_bin": "E:\\wamp\\bin\\php\\php5.5.12\\php.exe",#你的php位置 版本>5.50
    "space_around_exclamation_mark": true,
    "version": 3
}

```
DocBlockr(辅助生成函数文档注释) 配置:

```json

{
    "jsdocs_extra_tags": [],
    "jsdocs_extra_tags_go_after": false,
    "jsdocs_notation_map": 
    [
      {
        "prefix": "_",
        "tags": ["@access private"],
      },
      {
        "regex": ".*",
        "tags": ["@author xxx[xxx@yunyin.org]"]//换成你的名字和邮箱
      }
    ],
    "jsdocs_return_tag": "@return",
    "jsdocs_return_description": true,
    "jsdocs_param_description": true,
    "jsdocs_param_name": true,
    "jsdocs_spacer_between_sections": false,
    "jsdocs_per_section_indent": false,
    "jsdocs_min_spaces_between_columns": 1,
    "jsdocs_autoadd_method_tag": true,
    "jsdocs_lower_case_primitives": false,
    "jsdocs_short_primitives": false,
    "jsdocs_override_js_var": false,
    "jsdocs_decorate": true,
    "jsdocs_quick_open_inline": true,
}

```



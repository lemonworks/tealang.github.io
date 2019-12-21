# Tea语言 (Tealang, The Tea Programming Language)

```Tea
// 来自深圳的问候
echo "世界你好！"
```

## 简要介绍

Tea语言是一门新设计的计算机编程语言，采用强规范设计（规范即为语法），语法精炼简洁，并拥有简单的类型系统和单元模块体系。其目标是成为一个友好的，支持多端开发的编程语言，并尽量支持已有的常用编程语言生态，让开发者可以使用一门编程语言编写多个编程语言生态的程序，并能继续使用已有工作成果。

Tea语言经过精心设计，在尽量保持常用编程语言习惯的同时，力求让代码编写体验更轻松自然，对使用者更友好，让使用者可以更专注于创意实现。

目前为Tea语言早期版本，编译器使用PHP 7.2实现，通过编译生成PHP代码运行，可调用已有PHP库。
预计后期版本将使用Tea语言重新实现编译器，并支持编译生成PHP、JavaScript或其它编程语言的代码。

Tea语言主要有以下特点：
- 强规范设计，规范即语法，简洁清晰
- 内置单元模块（Unit）体系，基于单元模块组织程序文件，和访问控制
- 拥有简约的类型系统，支持类型推断，并在类型兼容性方面有一些特别处理
- 无普通全局变量设计，无需担心全局变量的污染问题
- 对字符串处理语法进行了特别设计，灵活、简单而强大，尤其方便用于Web开发
- 对流程控制语法进行了特别设计，简约而统一，代码更简洁
- 对运算符规则进行了简化设计，规则简单而统一
- 对面向对象特性进行了简化设计，语法简约而强大
- 静态编译型设计，编译时将进行类型推断和语法检查
- 通过编译生成目标编程语言代码的方式运行

Tea语言由创业者Benny设计开发，潘春孟（高级工程师/架构师）与刘景能（计算机博士）参与了早期设计与测试使用。

## 安装和使用

- 安装PHP 7.2+运行环境
- 安装好PHP后，将PHP执行文件所在目录添加到操作系统环境变量中
- 将Tea语言项目克隆到本地（或其它方式下载，但需保证项目目录名称为tea）
	```sh
	# clone with the Git client
	git clone https://github.com/tealang/tea.git
	```
- 将当前目录切换到tea的上级目录中，执行如下命令即可编译本文档程序：
	```sh
	# use the normal method
	php tea/bin/tea tea/docs
	```
- 如使用Mac或Linux系统，可使用Shebang方式，如：
	```sh
	# lets the scripts could be execute
	chmod +x tea/bin/*
	# use the Shebang method
	tea/bin/tea tea/docs
	```
- 新初始化一个Unit，如：
	```sh
	php tea/bin/tea --init myproject.com/hello
	```
- 在dist目录中可看到编译生成的目标文件

## 致谢

Tea语言从众多优秀的编程语言中获取到设计灵感，主要包括（排名不分先后）：
	PHP、Hack、JavaScript、TypeScript、Python、Swift、Kotlin、Go、Rust、Ruby、Java、C#、Pascal、C、Julia等。
在此向设计和维护这些编程语言的大师们致敬。


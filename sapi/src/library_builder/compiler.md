交叉编译


宿主机（host）

目标机（target）

prefix：交叉编译器的安装位置

xxx-xxxx-xxxxx 平台描述


交叉编译器

预处理器（preprocessor）
编译器前端（frontend）
            负责解析（parse）输入的源代码  负责语义（semantic checking）的检查  最终的结果常常是一个抽象的语法树（abstract syntax tree，或 AST）
编译器后端（backend）
           主要负责分析，优化中间代码（Intermediate representation）以及生成机器代码（Code Generation）

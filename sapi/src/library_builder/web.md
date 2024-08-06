## 浏览器立即变身为记事本

```javascript

data:text / html, <html contenteditable>edit me

```

## 一句话网页变灰

```css

html {
    filter: grayscale(100%);
}


```

## 网页超级模式

```javascript

// 用 document.designMode 可开启 Chrome 网页"上帝"模式，可编辑网页
document.designMode = "on";
//or
document.body.contentEditable = "true";
//or
document.documentElement.setAttribute("contenteditable", "true");

//或者浏览器地址栏执行这一句  也可以保存为书签
javascript:(() => {document.body.contentEditable = 'true', document.designMode = 'on'})();



```

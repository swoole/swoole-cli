<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Credits</title>
    <style>
        .product {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            line-height: 2.1rem;
        }

        .product span {
            margin-left: 0.4rem;
        }
    </style>
</head>
<body>
<h1 class="page-title" style="text-align: center">Credits</h1>
<div class="product">
    <span class="title">musl-libc</span>
    <span class="homepage">
        <a href="http://www.musl-libc.org/"
           target="_blank"
           rel="noopener noreferrer"
        >homepage</a>
    </span>
    <span class="manual">
        <a href="http://musl.libc.org/manual.html"
           target="_blank"
           rel="noopener noreferrer"
        >manual</a>
    </span>
    <input type="checkbox" hidden="hidden" id=""/>
    <label class="show" tabindex="0"></label>
    <span class="licence">
        <a
                href="http://git.musl-libc.org/cgit/musl/tree/COPYRIGHT"
                target="_blank"
                rel="noopener noreferrer"
        >licence</a>
    </span>
</div>
<div class="product">
    <span class="title">php</span>
    <span class="homepage"><a href="https://www.php.net/">homepage</a></span>
    <span class="manual"><a
                href="https://www.php.net/docs.php"
                target="_blank"
                rel="noopener noreferrer"
        >manual</a>
    </span>
    <input type="checkbox" hidden="hidden" id=""/>
    <label class="show" tabindex="0"></label>
    <span class="licence"><a
                href="https://github.com/php/php-src/blob/master/LICENSE"
                target="_blank"
                rel="noopener noreferrer"
        >licence</a>
    </span>
</div>
<?php
foreach ($this->libraryList as $item) {
    if (empty($item->license)) {
        continue;
    } else {
        ?>
        <div class="product">
            <span class="title"><?= $item->name ?></span>
            <span class="homepage">
            <a
                    href="<?= $item->homePage ?>"
                    target="_blank"
                    rel="noopener noreferrer"
            >homepage</a>
        </span>
            <span class="manual">
            <a
                    href="<?= $item->manual ?>"
                    target="_blank"
                    rel="noopener noreferrer"
            >manual</a>
        </span>
            <input type="checkbox" hidden="hidden" id=""/>
            <label class="show" tabindex="0"></label>
            <span class="licence">
            <a
                    href="<?= $item->license ?>"
                    target="_blank"
                    rel="noopener noreferrer"
            >licence</a>
        </span>
        </div>

        <?php
    }
}

foreach ($this->extensionList as $item) {
    if (empty($item->license)) {
        continue;
    } else {
        ?>
        <div class="product">
            <span class="title">php-ext-<?= $item->name ?></span>
            <span class="homepage"></span>
            <span class="manual">
                <a
                        href="<?= $item->manual ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                >manual</a>
            </span>
            <input type="checkbox" hidden="hidden" id=""/>
            <label class="show" tabindex="0"></label>
            <span class="licence">
                <a
                        href="<?= $item->license ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                >licence</a>
            </span>
        </div>
        <?php
    }
}
?>
</body>
</html>
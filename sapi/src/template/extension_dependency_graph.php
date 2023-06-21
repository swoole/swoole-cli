digraph "swoole_cli_diagram" {
    charset="UTF-8"
    center=true

    node [shape=rect ; fontsize = 18 ;overlap = false]
    edge [
        shape=box ;
        overlap = false ;

        color = "#51bf5b"
        arrowsize = 0.5
        style = "filled,setlinewidth(3)"
    ]

    graph [
        rankdir="LR"
        newrank = true,
        nodesep = 0.4,
        ranksep = 1,
        overlap = false
        splines = spline
    ]


    # style = invis 隐身
    # nodesep : 同一个 rank 中的相邻节点的最小距离，单位为英寸（=2.54 cm)。直线的不同端点属于不同的 rank；
    # ranksep : 相邻 rank 之间的距离；
    # rankdir: rank的指向，如 LR (left to right) or RL，或者 TB （top to bottom） or BT;
    {
        rank="same"
        cluster_ext_group
        cluster_lib_group
        docs
    }
    cluster_ext_group [style = invis]
    cluster_lib_group [style = invis]

    subgraph cluster_ext_group {
        lable="扩展组"
        node [
            color = "#c5955d"
            weight = 10,
            style = "filled,setlinewidth(5)"
            fillcolor = "#292929"
            fontcolor = "#30ba78"
            shape = ellipse
        ]
        edge [
            color = "#51bf5b"
            arrowsize = 0.5,
            labelfontname = "Ubuntu"
            style = "filled,setlinewidth(5)"

        ]


<?php
foreach ($this->extensionMap as $extension) {
    if (!empty($extension->deps)) {
        continue;
    }
    echo "        ";
    echo 'ext_' . $extension->name;
    $url = !empty($extension->homePage) ? $extension->homePage : $extension->manual;
    echo " [ URL = \"{$url}\" ;target=\"_blank\"; ]";
    echo PHP_EOL;
}
foreach ($this->extensionMap as $extension) {
    if (empty($extension->deps)) {
        continue;
    }
    echo "        ";
    echo 'ext_' . $extension->name;
    $url = !empty($extension->homePage) ? $extension->homePage : $extension->manual;
    echo " [ URL = \"{$url}\" ;target=\"_blank\"; ]";
    echo PHP_EOL;
}

?>

    }


    subgraph cluster_lib_group {
        graph [
            rankdir="RL"
        ]

        edge [
            color = "#61c2c5"
            color = "#8383cc"
        ]
        node [
            color = "#e27dd6ff"
            color = "#61c2c5"
            color = "#8383cc"

            weight = 10,
            style = "filled,setlinewidth(5)"
            fillcolor = "#292929"
            fontcolor = "#30ba78"
        ]
<?php
foreach ($this->libraryMap as $lib) {
    $libraryName = $lib->name;
    $libraryName = strpos($libraryName, 'lib') === 0 ? $libraryName : 'lib' . $libraryName;
    echo "        ";
    echo $libraryName;
    $url = !empty($lib->homePage) ? $lib->homePage : $lib->manual;
    echo " [ URL = \"{$url}\" ;target=\"_blank\"; ]";
    echo PHP_EOL;
}

?>

    }


    subgraph ext_dependency {
        rankdir="TB"
<?php
foreach ($this->extensionDependentLibraryMap as $extensionName => $libs) {
    foreach ($libs as $libraryName) {
        $libraryName = strpos($libraryName, 'lib') === 0 ? $libraryName : 'lib' . $libraryName;
        echo "        ";
        echo 'ext_' . $extensionName;
        echo '->';
        echo $libraryName;
        echo PHP_EOL;
    }
}

?>

    }


    subgraph lib_dependency {
        rankdir="LR"

        edge [
            color="#0d6efd"
            color="#732e7e";

        ]

<?php
foreach ($this->libraryList as $lib) {
    foreach ($lib->deps as $libraryName) {
        $name = $lib->name;
        $name = strpos($name, 'lib') === 0 ? $name : 'lib' . $name;
        echo "        ";
        echo $name;
        echo "->";
        $libraryName = strpos($libraryName, 'lib') === 0 ? $libraryName : 'lib' . $libraryName;
        echo $libraryName;
        echo PHP_EOL;
    }
}

?>
}


    subgraph docs {
        docs [label = "graphviz 文档"]
        node [
            weight = 10,
            style = "filled,setlinewidth(5)"
            fillcolor = "#292929"
            fontcolor = "#30ba78"
        ]
        docs->graphviz_reference
        docs->graphviz_gallery
        graphviz_reference [URL = "https://www.graphviz.org/documentation/", target="_blank"]
        graphviz_gallery [URL = "https://www.graphviz.org/gallery/", target="_blank"]
    }
}
